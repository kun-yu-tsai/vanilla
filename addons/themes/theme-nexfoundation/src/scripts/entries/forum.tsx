/**
 * @copyright 2009-2019 Vanilla Forums Inc.
 * @license GPL-2.0-only
 */

import "../../scss/custom.scss";
import React from "react";
import { Advertisement, MobileAdvertisement } from "../components/advertisement";
import { onContent, onReady } from "@vanilla/library/src/scripts/utility/appUtils";
import { mountReact } from "@vanilla/react-utils";
import { addHamburgerNavGroup } from "@library/flyouts/Hamburger";
import { registerReducer } from "@library/redux/reducerRegistry";
import { nexReducer, useNexState } from "../redux/NexReducer";
import { CategoriesModule } from "../components/categories";
import { TagsModule } from "../components/tags";
import { IUserFragment } from "@library/@types/api/users";
import { UserPhoto, UserPhotoSize } from "@library/headers/mebox/pieces/UserPhoto";
import { Bookmark, IBookmark } from "../components/bookmark";
import gdn from "@library/gdn";

interface ITag {
    name: string;
    url: string;
}

registerReducer("nex", nexReducer);

onReady(() => {
    bootstrap();
});

onContent(() => {
    mountArticleTags();
    mountArticleUser();
    mountArticleBookmark();
});

function bootstrap() {
    const adElement = document.getElementById("nex-advertisement");
    if (adElement) {
        mountReact(<Advertisement />, adElement, undefined);
    }
    const categoryNodes = document.querySelectorAll(".hot-forum-root_topic");
    for (const categoryNode of categoryNodes) {
        categoryNode?.addEventListener("click", clickAnchorInside);
    }

    const anchorsInside = document.querySelectorAll(".hot-forum-root_topic .ItemLink");
    for (const anchorInside of anchorsInside) {
        anchorInside?.addEventListener("click", e => {
            e.stopPropagation();
        });
    }
    initHamburger();
}

function initHamburger() {
    addHamburgerNavGroup(CategoriesModule);
    addHamburgerNavGroup(TagsModule);
    addHamburgerNavGroup(MobileAdvertisement);
}

function clickAnchorInside(this: any) {
    const anchor = this.getElementsByTagName("A")[0];
    anchor.click();
}

function mountArticleTags() {
    const discussions = document.querySelector("ul.DataList.Discussions");
    if (!discussions) {
        return;
    }
    for (const discussion of discussions.querySelectorAll("li.ItemDiscussion")) {
        const meta = JSON.parse((discussion as HTMLElement).dataset.meta!);
        const tags: ITag[] = meta.tags ? meta.tags : [];
        const tagNode = discussion.querySelector(`#${discussion.id.replace("Discussion", "tag")}`);
        if (tagNode === null || tags.length <= 0 || tagNode.getAttribute("mounted")) {
            continue;
        }
        tagNode.setAttribute("mounted", true);
        mountReact(
            <ul className="TagCloud">
                {tags.map((tag, idx) => {
                    return (
                        <li className="TagCloud-Item" key={`tag_${idx}`}>
                            <a href={tag.url} className={`tag Tag_${tag.name}`}>
                                {tag.name}
                            </a>
                        </li>
                    );
                })}
            </ul>,
            tagNode as HTMLElement,
        );
    }
}

function mountArticleUser() {
    const userAnchors = document.querySelectorAll("span.MItem.Author");
    if (!userAnchors) {
        return;
    }
    for (const userAnchor of userAnchors) {
        const userMeta = (userAnchor as HTMLElement).dataset.user;
        const userURL = (userAnchor as HTMLElement).dataset.url || "";
        if (userMeta === null || userMeta === undefined || userAnchor.getAttribute("mounted")) {
            continue;
        }
        userAnchor.setAttribute("mounted", true);
        const user: IUserFragment = JSON.parse(userMeta);
        const size = gdn.meta.DiscussionID ? UserPhotoSize.MEDIUM : UserPhotoSize.SMALL;
        mountReact(
            <div className="UserAnchor">
                <UserPhoto userInfo={user} size={size}></UserPhoto>
                <a href={userURL}>{user.name}</a>
            </div>,
            userAnchor as HTMLElement,
        );
    }
}

function mountArticleBookmark() {
    const bookmarkAnchors = document.querySelectorAll("span.MItem.MCount.ViewCount");
    for (const bookmarkAnchor of bookmarkAnchors) {
        const discussionMeta = (bookmarkAnchor as HTMLElement).dataset.discussion;
        if (discussionMeta === null || discussionMeta === undefined || bookmarkAnchor.getAttribute("mounted")) {
            continue;
        }
        const discussion: IBookmark = JSON.parse(discussionMeta);
        bookmarkAnchor.setAttribute("mounted", true);
        mountReact(
            <Bookmark
                bookmarked={discussion.bookmarked}
                discussionID={discussion.discussionID}
                countBookmarks={discussion.countBookmarks}
            />,
            bookmarkAnchor as HTMLElement,
        );
    }
}

/**
 * @copyright 2009-2019 Vanilla Forums Inc.
 * @license GPL-2.0-only
 */

import "../../scss/custom.scss";
import React from "react";
import { Advertisement, MobileAdvertisement } from "../components/advertisement";
import { onContent } from "@vanilla/library/src/scripts/utility/appUtils";
import { mountReact } from "@vanilla/react-utils";
import { addHamburgerNavGroup } from "@library/flyouts/Hamburger";
import { registerReducer } from "@library/redux/reducerRegistry";
import { nexReducer, useNexState } from "../redux/NexReducer";
import { CategoriesModule } from "../components/categories";
import { TagsModule } from "../components/tags";
interface ITag {
    name: string;
    url: string;
}

registerReducer("nex", nexReducer);

onContent(() => {
    bootstrap();
    mountArticleTags();
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
        if (tagNode === null || tags.length <= 0) {
            continue;
        }
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

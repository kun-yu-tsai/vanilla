import React from "react";
import { StoryContent } from "@library/storybook/StoryContent";
import { StoryHeading } from "@library/storybook/StoryHeading";
import { storiesOf } from "@storybook/react";
import { StoryTiles } from "@library/storybook/StoryTiles";
import { StoryTileAndTextCompact } from "@library/storybook/StoryTileAndTextCompact";
import { BookmarkedIcon, BookmarkIcon } from "./bookmark";
import { styleFactory } from "@library/styles/styleUtils";

const story = storiesOf("NEX Theme", module);

function nexStyles() {
    const style = styleFactory("storybook");
    const bookmark = style("bookmark", {
        width: "20px",
        height: "20px",
        fill: "#FF3559",
        stroke: "#FF3559",
    });
    return {
        bookmark,
    };
}

const NexClasses = nexStyles();

story.add("Icons", () => {
    return (
        <StoryContent>
            <StoryHeading>Icons</StoryHeading>
            <StoryTiles>
                <StoryTileAndTextCompact text="Bookmarked">
                    <BookmarkedIcon className={NexClasses.bookmark}></BookmarkedIcon>
                </StoryTileAndTextCompact>
                <StoryTileAndTextCompact text="Bookmark">
                    <BookmarkIcon className={NexClasses.bookmark}></BookmarkIcon>
                </StoryTileAndTextCompact>
                <StoryTileAndTextCompact text="Bookmark Loading">
                    <BookmarkIcon className={NexClasses.bookmark} loading={true}></BookmarkIcon>
                </StoryTileAndTextCompact>
            </StoryTiles>
        </StoryContent>
    );
});

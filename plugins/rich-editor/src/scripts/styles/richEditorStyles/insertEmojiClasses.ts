/*
 * @author Stéphane LaFlèche <stephane.l@vanillaforums.com>
 * @copyright 2009-2019 Vanilla Forums Inc.
 * @license GPL-2.0-only
 */

import { richEditorVariables } from "@rich-editor/styles/richEditorStyles/richEditorVariables";
import { globalVariables } from "@library/styles/globalStyleVars";
import { appearance, unit } from "@library/styles/styleHelpers";
import styleFactory from "@library/styles/styleFactory";
import { em, viewHeight } from "csx";
import memoize from "lodash/memoize";

export const insertEmojiClasses = memoize((theme?: object) => {
    const globalVars = globalVariables(theme);
    const vars = richEditorVariables(theme);
    const style = styleFactory("insertEmoji");

    const root = style({
        ...appearance(),
        display: "flex",
        alignItems: "center",
        justifyContent: "center",
        fontSize: unit(globalVars.icon.sizes.default),
        textAlign: "center",
        overflow: "hidden",
        border: 0,
        opacity: globalVars.states.text.opacity,
        cursor: "pointer",
        $nest: {
            ".fallBackEmoji": {
                display: "block",
                margin: "auto",
            },
            "&:hover, &:focus, &:active, &.focus-visible": {
                opacity: 1,
            },
            ".safeEmoji": {
                display: "block",
                height: unit(globalVars.icon.sizes.default),
                width: unit(globalVars.icon.sizes.default),
                margin: "auto",
            },
        },
    });

    const body = style("body", {
        height: unit(vars.emojiBody.height),
        maxHeight: viewHeight(80),
    });

    const popoverDescription = style("popoverDescription", {
        marginBottom: ".5em",
    });

    return { root, body, popoverDescription };
});

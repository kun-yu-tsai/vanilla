import DropDownSection from "@library/flyouts/items/DropDownSection";
import { t } from "@library/utility/appUtils";
import React, { CSSProperties } from "react";
import { useNexState } from "../redux/NexReducer";

const TagsBox: CSSProperties = {
    marginLeft: "10px",
    padding: "10px",
};

export function TagsModule(props) {
    const state = useNexState();
    const tags = state.tags.data ? state.tags.data : [];
    return (
        <DropDownSection title={t("Popular Tags")}>
            <div className="Box Tags" style={TagsBox}>
                <ul className="TagCloud">
                    {tags.map((tag, index) => {
                        return (
                            <li key={`tag-${index}`}>
                                <a href={`/discussions/tagged/${tag}`}>{tag}</a>
                            </li>
                        );
                    })}
                </ul>
            </div>
        </DropDownSection>
    );
}

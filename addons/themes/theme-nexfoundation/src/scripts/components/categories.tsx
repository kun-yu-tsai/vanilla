import React from "react";
import DropDownSection from "@library/flyouts/items/DropDownSection";
import DropDownItemLink from "@library/flyouts/items/DropDownItemLink";
import { useNexState } from "../redux/NexReducer";
import { t } from "@library/utility/appUtils";

export function CategoriesModule(props) {
    const state = useNexState();
    const categories = state.categories.data ? state.categories.data : [];
    return (
        <DropDownSection title={t("Categories")}>
            {categories.map((category, index) => {
                return (
                    <DropDownItemLink key={`category-${index}`} to={`/categories/${category}`}>
                        {category}
                    </DropDownItemLink>
                );
            })}
        </DropDownSection>
    );
}

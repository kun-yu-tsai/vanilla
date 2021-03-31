import "../scss/custom.scss";
import React from "react";
import { onContent } from "@library/utility/appUtils";
import { LikeButton } from "../components/LikeButton";
import { mountReact } from "@vanilla/react-utils";

onContent(() => {
    bootstrap();
});

function bootstrap() {
    for (const el of document.getElementsByName("likeButton")) {
        if (el.getAttribute("mounted")) {
            continue;
        }
        el.setAttribute("mounted", true);
        const meta = JSON.parse(el.dataset.meta || "");
        mountReact(
            <LikeButton record_type={meta.type} record_id={meta.id} liked={meta.liked} count={meta.count} />,
            el,
        );
    }
}

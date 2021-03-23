import "../scss/custom.scss";
import React from "react";
import { onContent } from "@library/utility/appUtils";
import { LikeButton } from "../components/LikeButton";
import { mountReact } from "@vanilla/react-utils";
import { map } from "lodash";

onContent(() => {
    bootstrap();
});

function bootstrap() {
    map(document.getElementsByName("likeButton"), el => {
        const meta = JSON.parse(el.dataset.meta || "");
        mountReact(
            <LikeButton record_type={meta.type} record_id={meta.id} liked={meta.liked} count={meta.count} />,
            el,
        );
    });
}

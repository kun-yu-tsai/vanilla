import React from "react";
import classNames from "classnames";

export function ThumbUpIcon(props: { className?: string; outlined?: boolean }) {
    if (props.outlined !== undefined && props.outlined) {
        return (
            <svg
                className={classNames(props.className)}
                xmlns="http://www.w3.org/2000/svg"
                height="18"
                viewBox="0 0 24 24"
                width="18"
            >
                <path d="M0 0h24v24H0V0z" fill="none" />
                <path d="M1 21h4V9H1v12zm22-11c0-1.1-.9-2-2-2h-6.31l.95-4.57.03-.32c0-.41-.17-.79-.44-1.06L14.17 1 7.59 7.59C7.22 7.95 7 8.45 7 9v10c0 1.1.9 2 2 2h9c.83 0 1.54-.5 1.84-1.22l3.02-7.05c.09-.23.14-.47.14-.73v-2z" />
            </svg>
        );
    } else {
        return (
            <svg
                className={classNames(props.className)}
                xmlns="http://www.w3.org/2000/svg"
                height="18"
                viewBox="0 0 24 24"
                width="18"
            >
                <path d="M0 0h24v24H0V0z" fill="none" />
                <path d="M13.11 5.72l-.57 2.89c-.12.59.04 1.2.42 1.66.38.46.94.73 1.54.73H20v1.08L17.43 18H9.34c-.18 0-.34-.16-.34-.34V9.82l4.11-4.1M14 2L7.59 8.41C7.21 8.79 7 9.3 7 9.83v7.83C7 18.95 8.05 20 9.34 20h8.1c.71 0 1.36-.37 1.72-.97l2.67-6.15c.11-.25.17-.52.17-.8V11c0-1.1-.9-2-2-2h-5.5l.92-4.65c.05-.22.02-.46-.08-.66-.23-.45-.52-.86-.88-1.22L14 2zM4 9H2v11h2c.55 0 1-.45 1-1v-9c0-.55-.45-1-1-1z" />
            </svg>
        );
    }
}

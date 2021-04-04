import React from "react";
import classNames from "classnames";

export function BookmarkedIcon(props: { className?: string }) {
    return (
        <svg width="15" height="20" viewBox="0 0 15 20" className={props.className} xmlns="http://www.w3.org/2000/svg">
            <path d="M12.119 0.25C12.6369 0.25 13.1335 0.478273 13.4996 0.884602C13.8658 1.29093 14.0715 1.84203 14.0715 2.41667V19.75L7.23786 16.5L0.404221 19.75V2.41667C0.404221 1.84203 0.609927 1.29093 0.976086 0.884602C1.34224 0.478273 1.83886 0.25 2.35669 0.25H12.119ZM6.26163 4.58333V6.75H4.30916V8.91667H6.26163V11.0833H8.2141V8.91667H10.1666V6.75H8.2141V4.58333H6.26163Z" />
        </svg>
    );
}

export function BookmarkIcon(props: { className?: string; loading?: boolean }) {
    return (
        <svg
            className={props.className}
            xmlns="http://www.w3.org/2000/svg"
            viewBox="0 0 12.733 16.394"
            aria-hidden="true"
        >
            <path
                strokeWidth="2"
                fill="none"
                d="M1.05.5H11.683a.55.55,0,0,1,.55.55h0V15.341a.549.549,0,0,1-.9.426L6.714,12a.547.547,0,0,0-.7,0L1.4,15.767a.55.55,0,0,1-.9-.426V1.05A.55.55,0,0,1,1.05.5z"
            ></path>
            {props.loading && (
                <path
                    d="M11.7,0.5H6.4v11.4c0.1,0,0.2,0,0.3,0.1l4.6,3.8c0.1,0.1,0.2,0.1,0.4,0.1c0.3,0,0.5-0.2,0.5-0.6V1.1C12.2,0.7,12,0.5,11.7,0.5z"
                    className={classNames("Loading")}
                ></path>
            )}
        </svg>
    );
}

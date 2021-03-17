/**
 * @copyright 2009-2019 Vanilla Forums Inc.
 * @license GPL-2.0-only
 */

import React, { CSSProperties, Fragment } from "react";
import gdn from "@library/gdn";
export class Advertisement extends React.Component<{ mobile: boolean }> {
    style: CSSProperties = {
        width: this.props.mobile ? "100%" : "",
    };
    open = () => {
        window.open("https://www.nexf.org/");
    };

    render() {
        return (
            <Fragment>
                <img
                    style={this.style}
                    className="banner"
                    src={`${gdn.meta.currentThemePath}/assets/ad.svg`}
                    alt="nex foundation"
                    onClick={() => this.open()}
                />
            </Fragment>
        );
    }
}

export class MobileAdvertisement extends React.Component {
    style: CSSProperties = {
        display: "flex",
        justifyContent: "center",
        alignItems: "center",
        marginTop: "20px",
    };

    render() {
        return (
            <div style={this.style}>
                <Advertisement mobile={true} />
            </div>
        );
    }
}

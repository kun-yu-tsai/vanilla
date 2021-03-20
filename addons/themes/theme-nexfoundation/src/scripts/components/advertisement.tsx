/**
 * @copyright 2009-2019 Vanilla Forums Inc.
 * @license GPL-2.0-only
 */

import React, { CSSProperties, Fragment } from "react";
import { INexStoreState } from "../redux/NexReducer";
import { connect } from "react-redux";

const mapStateToProps = (state: INexStoreState) => {
    const meta = state.nex.meta.data;
    return {
        currentThemePath: meta?.currentThemePath,
    };
};

interface IAdvertisementProps {
    mobile?: boolean;
}

type IProps = IAdvertisementProps & ReturnType<typeof mapStateToProps>;

export class BaseAdvertisement extends React.Component<IProps> {
    style: CSSProperties = {
        width: this.props.mobile ? "85%" : "",
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
                    src={`${this.props.currentThemePath}/assets/ad.svg`}
                    alt="nex foundation"
                    onClick={() => this.open()}
                />
            </Fragment>
        );
    }
}
const withRedux = connect(mapStateToProps);
export const Advertisement = withRedux(BaseAdvertisement);

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

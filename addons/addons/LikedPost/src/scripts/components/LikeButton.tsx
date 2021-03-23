import { ThumbUpIcon } from "./icons";
import React from "react";
import apiv2 from "@library/apiv2";

interface ILikeButtonProps {
    record_type: string;
    record_id: number;
    liked: boolean;
    count: number;
}

interface ILikeButtonState {
    liked: boolean;
    count: number;
}

export class LikeButton extends React.Component<ILikeButtonProps, ILikeButtonState> {
    state: ILikeButtonState;
    constructor(props: ILikeButtonProps) {
        super(props);
        this.state = { liked: props.liked, count: props.count };
    }

    clickLike = () => {
        apiv2
            .patch(`/like/${this.props.record_id}`, { type: this.props.record_type, like: !this.state.liked })
            .then(response => {
                const liked = response.data.liked > 0;
                const count = liked ? this.state.count + 1 : this.state.count - 1;
                this.setState({ ...this.state, liked: liked, count: count });
            });
    };

    render() {
        return (
            <div className="like-button" onClick={this.clickLike}>
                <ThumbUpIcon
                    outlined={this.state.liked}
                    className={`like-button-icon ${this.state.liked ? "liked" : ""}`}
                />
                <span className="like-count">{this.state.count}</span>
            </div>
        );
    }
}

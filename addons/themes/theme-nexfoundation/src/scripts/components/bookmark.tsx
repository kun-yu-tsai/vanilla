import React from "react";
import { BookmarkIcon, BookmarkedIcon } from "../icons/bookmark";
import { t } from "@library/utility/appUtils";
import apiv2 from "@library/apiv2";

export interface IBookmark {
    bookmarked: boolean;
    discussionID: number;
    countBookmarks: number;
}

interface IBookmarkState {
    bookmarked: boolean;
    countBookmark: number;
    loading: boolean;
}

interface IBookmarkProps {
    bookmarked: boolean;
    discussionID: number;
    countBookmarks: number;
}

export class Bookmark extends React.Component<IBookmarkProps, IBookmarkState> {
    state: IBookmarkState;
    constructor(props: IBookmarkProps) {
        super(props);
        this.state = {
            bookmarked: props.bookmarked,
            countBookmark: props.countBookmarks,
            loading: false,
        };
    }
    onClick = () => {
        this.setState({ ...this.state, loading: true });
        apiv2
            .put(`/discussions/${this.props.discussionID}/bookmark`, {
                bookmarked: !this.state.bookmarked,
            })
            .then(response => {
                const bookmarked = response.data.bookmarked;
                const count = bookmarked ? this.state.countBookmark + 1 : this.state.countBookmark - 1;
                this.setState({ ...this.state, bookmarked: bookmarked, countBookmark: count });
            })
            .finally(() => {
                this.setState({ ...this.state, loading: false });
            });
    };
    render() {
        return (
            <div className="BookmarkIconBox" onClick={this.onClick}>
                {this.state.bookmarked ? (
                    <BookmarkedIcon className="BookmarkIcon" />
                ) : (
                    <BookmarkIcon className="BookmarkIcon" loading={this.state.loading} />
                )}
                <span>{this.state.countBookmark}</span>
            </div>
        );
    }
}

import { ILoadable, LoadStatus } from "@library/@types/api/core";
import NexActions from "./NexAction";
import produce from "immer";
import { reducerWithInitialState } from "typescript-fsa-reducers";
import { useSelector } from "react-redux";
import { ICoreStoreState } from "@library/redux/reducerRegistry";

export interface INexTags {
    tags: string[];
}

export interface INexCategory {
    names: string[];
}

export interface INexMeta {
    currentThemePath: string;
}

export interface INexState {
    tags: ILoadable<string[]>;
    categories: ILoadable<string[]>;
    meta: ILoadable<INexMeta>;
}
export interface INexStoreState extends ICoreStoreState {
    nex: INexState;
}

export const INITIAL_NEX_STATE: INexState = {
    tags: { status: LoadStatus.PENDING },
    categories: { status: LoadStatus.PENDING },
    meta: { status: LoadStatus.PENDING },
};

export const nexReducer = produce(
    reducerWithInitialState(INITIAL_NEX_STATE)
        .case(NexActions.getTags.done, (state, payload) => {
            state.tags.status = LoadStatus.SUCCESS;
            state.tags.data = payload.result.tags;
            return state;
        })
        .case(NexActions.getCategory.done, (state, payload) => {
            state.categories.status = LoadStatus.SUCCESS;
            state.categories.data = payload.result.names;
            return state;
        })
        .case(NexActions.getMeta.done, (state, payload) => {
            state.meta.status = LoadStatus.SUCCESS;
            state.meta.data = payload.result;
            return state;
        }),
);

export function useNexState() {
    return useSelector((state: INexStoreState) => {
        return state.nex;
    });
}

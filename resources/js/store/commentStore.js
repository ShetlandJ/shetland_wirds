import { defineStore } from 'pinia';

export const useStore = defineStore('commentStore', {
    state: () => {
        return {
            childEditing: false,
        }
    },
    getters: {
        getChildEditing: (state) => state.childEditing,
    },
    actions: {
        setChildEditing(value) {
            this.childEditing = value;
        },
    }
});

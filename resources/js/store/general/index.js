import { v4 as uuidv4 } from "uuid";
export default {
    namespaced: true,
    state: {
        parentsmenus: [],
        linkhistories: [],
        activemenu: null,
    },
    mutations: {
        set_open_menu(state, payload) {
            var findParent = state.parentsmenus.findIndex(
                (el) => el.name === payload.name
            );

            var idLink = uuidv4();
            var links = {
                id: idLink,
                name: payload.links.name,
                url: payload.links.url,
                parent: payload.links.parent,
                title: payload.links.title,
                icon: payload.links.icon,
                params: payload.links.params,
                query: payload.links.query
            };

            if (findParent != -1) {
                if (payload.name == payload.links.name) {
                    var findMenu = state.parentsmenus[
                        findParent
                    ].links.findIndex(
                        (el) =>
                            el.name === payload.links.name &&
                            JSON.stringify(el.params) ===
                                JSON.stringify(payload.links.params)
                    );

                    if (findMenu != -1) {
                        state.activemenu = state.parentsmenus[findParent].links[findMenu].id;
                        return;
                    } else {
                        state.parentsmenus[findParent].links.push(
                            payload.links
                        );
                        state.linkhistories.push(payload.links);
                        state.activemenu = idLink;
                        return;
                    }
                } else {
                    state.parentsmenus[findParent].links.push(links);
                    state.linkhistories.push(links);
                    state.activemenu = idLink;
                    return;
                }
            } else {
                state.parentsmenus.push({
                    name: payload.name,
                    title: payload.title,
                    icon: payload.icon,
                    links: [links],
                });

                state.linkhistories.push(links);
                state.activemenu = idLink;
                return;
            }
        },

        remove_menu(state, payload) {
            var findParent = state.parentsmenus.findIndex(
                (el) => el.name === payload.parent
            );

            if (findParent != -1) {
                var findMenu = state.parentsmenus[findParent].links.findIndex(
                    (el) => el.id === payload.id
                );

                if (findMenu != -1) {
                    // Hapus dari parentsmenus
                    state.parentsmenus[findParent].links.splice(findMenu, 1);

                    // Jika links kosong, hapus parent
                    if (state.parentsmenus[findParent].links.length < 1) {
                        state.parentsmenus.splice(findParent, 1);
                    }

                    // Hapus dari linkhistories
                    state.linkhistories = state.linkhistories.filter(
                        (history) => !(history.id === payload.id)
                    );
                }
            }
        },

        remove_parent(state, payload) {
            var findParent = state.parentsmenus.findIndex(
                (el) => el.name === payload.name
            );

            if (findParent != -1) {
                // Hapus dari parentsmenus
                state.parentsmenus.splice(findParent, 1);

                // Hapus semua link terkait di linkhistories
                state.linkhistories = state.linkhistories.filter(
                    (history) => history.parent !== payload.name
                );
            }
        },

        set_active_menu(state, payload) {
            state.activemenu = payload;
        },

        remove_menu_id(state, payload) {
            state.parentsmenus.forEach((parentMenu) => {
                const index = parentMenu.links.findIndex(
                    (link) => link.id === payload
                );

                if (index !== -1) {
                    parentMenu.links.splice(index, 1);

                    if (parentMenu.links.length === 0) {
                        const parentIndex = state.parentsmenus.findIndex(
                            (menu) => menu.name === parentMenu.name
                        );
                        state.parentsmenus.splice(parentIndex, 1);
                    } else {
                        let beforeMenu =
                            index > 0
                                ? parentMenu.links[index - 1]
                                : parentMenu.links[index + 1];

                        if (beforeMenu) {
                            state.activemenu = beforeMenu.id;
                        }
                    }
                }
            });
        },
    },
    getters: {
        get_tabs_menus: (state) => state.parentsmenus,
        get_histories: (state) => state.linkhistories,
        get_active_menu: (state) => state.activemenu,
    },
    actions: {
        set_open_menu: ({ commit }, payload) =>
            commit("set_open_menu", payload),
        set_active_menu: ({ commit }, payload) =>
            commit("set_active_menu", payload),
        remove_menu: ({ commit }, payload) => commit("remove_menu", payload),
        remove_parent: ({ commit }, payload) =>
            commit("remove_parent", payload),
        remove_menu_id: ({ commit }, payload) =>
            commit("remove_menu_id", payload),
    },
};

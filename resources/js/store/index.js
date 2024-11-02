import Vue from "vue";
import Vuex from "vuex";
import axios from "axios";

Vue.use(Vuex);

export default new Vuex.Store({
    state: {
        cantTareas: 0,
        fechaActual: "",
        usuario: {},
        colaborador: {},
        menuP: [],
        menu2: [],
        autorizado: false,
        mostrarModal: false,
        token: null,
        accessURL: false,
        isAccess: false,
    },
    mutations: {
        validarURL(state, url) {
            let rpta = false;
            const rutas = JSON.parse(localStorage.getItem("menuPath"));
            if (rutas != null) {
                rutas.forEach((el) => {
                    if (el.accion == url) {
                        rpta = true;
                    }
                });
            }
            if (rutas != null && (url == "/" || url == "/#")) {
                rpta = true;
            }

            if (rutas != null && !rpta) {
                rutas.forEach((el) => {
                    if (url.includes(`${el.accion}/`)) {
                        rpta = true;
                    }
                });
            }

            state.accessURL = rpta;
        },
        async accessSession(state, r) {
            let bandStatus = false;
            // if (r.code == "0") {
            if (r) {
                bandStatus = true;
            }
            // }
            state.isAccess = bandStatus;
        },
    },
    actions: {
        validarURL({ commit }, url) {
            commit("validarURL", url);
        },
        async accessSession({ commit }) {
            let response = await axios.get(`/validarsession`);
            let r = response.data.estado;
            commit("accessSession", r);
        },
    },
    getters: {
        isValidURL(state) {
            return state.accessURL;
        },
        isSessionActive(state) {
            return state.isAccess;
        },
    },
});

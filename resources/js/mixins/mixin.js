// eslint-disable-next-line no-unused-vars
import axios from "axios";
export const misMixins = {
    data() {
        return {
            estado: 0,
        };
    },
    methods: {
        atras: function (url) {
            this.$router.replace(url);
        },
        emitirPintado() {
            // eslint-disable-next-line no-undef
            $(".menu-item").removeClass("active-sub active");
            // eslint-disable-next-line no-undef
            $("#inicio").addClass("active-sub active");
            // eslint-disable-next-line no-undef
            $("#mainnav-container").click();
        },
        inicializarTooltips() {
            $(document).ready(function() {
                $('[data-toggle="tooltip"]').tooltip();
            });
        },
        animar() {
            // eslint-disable-next-line no-undef
            // alert(this.estado + screen.width)
            /*if (screen.width > 767) {
                if (this.estado % 2 === 0) {
                    // eslint-disable-next-line no-undef
                    $('.mainnav-profile').css('opacity', '1')
                    // eslint-disable-next-line no-undef
                    $('.mainnav-profile').css('max-height', '350px')
                    // eslint-disable-next-line no-undef
                    $('#cantTareas').css('opacity', '1')
                    // this.estado = 0
                } else {
                    // eslint-disable-next-line no-undef
                    $('.mainnav-profile').css('opacity', '0')
                    // eslint-disable-next-line no-undef
                    $('.mainnav-profile').css('max-height', '0')
                    // eslint-disable-next-line no-undef
                    $('#cantTareas').css('opacity', '0')
                    // this.estado = 1
                }
                this.estado++
            } else {
                // eslint-disable-next-line no-undef
                $('.mainnav-profile').css('opacity', '1')
                // eslint-disable-next-line no-undef
                $('.mainnav-profile').css('max-height', '350px')
                // eslint-disable-next-line no-undef
                $('#cantTareas').css('opacity', '1')
            }*/

            if (screen.width < 1023) {
                let el = document.querySelector(".nav-menu-main menu-toggle");
                // el.classList.toggle('is-active')
                // console.log('entramos...', el);
                // let el = document.getElementById('inicio')
                // el.click()
            }
        },
        aMayusculas(e) {
            let tecla = e.target.value;
            e.target.value = tecla.toUpperCase();
            // alert(tecla);
            // let key = e.keyCode || e.which
            // let tecla = String.fromCharCode(key).toUpperCase()
            // return;
        },
        soloLetras(e) {
            let key = e.keyCode || e.which;
            let tecla = String.fromCharCode(key).toLowerCase();
            let letras = " áéíóúabcdefghijklmnñopqrstuvwxyz";
            let especiales = [8, 37, 39, 46];

            // eslint-disable-next-line camelcase
            let tecla_especial = false;
            for (var i in especiales) {
                if (key === especiales[i]) {
                    // eslint-disable-next-line camelcase
                    tecla_especial = true;
                    break;
                }
            }

            // eslint-disable-next-line camelcase
            if (letras.indexOf(tecla) === -1 && !tecla_especial) {
                e.preventDefault();
                // return false
            }
        },
        soloLetrasNumerosDocumento(e) {
            let key = e.keyCode || e.which;
            let tecla = String.fromCharCode(key).toLowerCase();
            let letrasNumeros =
                "abcdefghijklmnñopqrstuvwxyz0123456789-";
            let especiales = [8, 37, 39, 46, 64]; // se agrego para correo
            // eslint-disable-next-line camelcase
            let tecla_especial = false;
            for (var i in especiales) {
                if (key === especiales[i]) {
                    // eslint-disable-next-line camelcase
                    tecla_especial = true;
                    break;
                }
            }

            if (letrasNumeros.indexOf(tecla) === -1 && !tecla_especial) {
                e.preventDefault();
                return false;
            }

            return true;
        },
        soloLetrasNumeros(e) {
            let key = e.keyCode || e.which;
            let tecla = String.fromCharCode(key).toLowerCase();
            let letrasNumeros =
                " áéíóúabcdefghijklmnñopqrstuvwxyz0123456789#-,";
            let especiales = [8, 37, 39, 46, 64]; // se agrego para correo
            // eslint-disable-next-line camelcase
            let tecla_especial = false;
            for (var i in especiales) {
                if (key === especiales[i]) {
                    // eslint-disable-next-line camelcase
                    tecla_especial = true;
                    break;
                }
            }

            if (letrasNumeros.indexOf(tecla) === -1 && !tecla_especial) {
                e.preventDefault();
                return false;
            }

            return true;
        },
        soloCL(e) {
            let key = window.Event ? e.which : e.keyCode;
            let tecla = String.fromCharCode(key).toLowerCase();
            // alert(tecla)
            let caracteresValidos = "ci";
            // eslint-disable-next-line camelcase
            if (caracteresValidos.indexOf(tecla) === -1) {
                e.preventDefault();
                return false;
            }

            return true;
        },
        validarNroDocumento(tipo, e) {
            if (tipo === 'CE') {
                return this.soloLetrasNumerosDocumento(e);
            } 

            return this.soloNumeros(e);
        },
        soloNumeros(e) {
            let key = window.Event ? e.which : e.keyCode;
            let tecla = String.fromCharCode(key).toLowerCase();
            // alert(tecla)
            let caracteresValidos = "0123456789";
            // eslint-disable-next-line camelcase
            if (caracteresValidos.indexOf(tecla) === -1) {
                e.preventDefault();
                return false;
            }

            return true;
        },
        soloNumerosGuion(e) {
            let key = window.Event ? e.which : e.keyCode;
            let tecla = String.fromCharCode(key).toLowerCase();
            // alert(tecla)
            let caracteresValidos = "0123456789-";
            // eslint-disable-next-line camelcase
            if (caracteresValidos.indexOf(tecla) === -1) {
                e.preventDefault();
                return false;
            }

            return true;
        },
        getRandomNumber(min, max) {
            return Math.floor(Math.random() * (max - min)) + min;
        },

        // soloLetrasNumeros(e) {
        //     let key = e.keyCode || e.which
        //     let tecla = String.fromCharCode(key).toLowerCase()
        //     let letrasNumeros = ' áéíóúabcdefghijklmnñopqrstuvwxyz0123456789,#'
        //     let especiales = [8, 37, 39, 46, 13]
        //     // eslint-disable-next-line camelcase
        //     let tecla_especial = false
        //     for (var i in especiales) {
        //         if (key === especiales[i]) {
        //             // eslint-disable-next-line camelcase
        //             tecla_especial = true
        //             break
        //         }
        //     }
        //     // eslint-disable-next-line camelcase
        //     if (letrasNumeros.indexOf(tecla) === -1 && !tecla_especial) {
        //         e.preventDefault()
        //     }
        // },
        obtenerFechaActual() {
            // this.$store.state.fechaActual =
            var f = new Date();
            return (
                f.getFullYear() +
                "-" +
                (f.getMonth() + 1 < 10
                    ? "0" + (f.getMonth() + 1)
                    : f.getMonth() + 1) +
                "-" +
                (f.getDate() < 10 ? "0" + f.getDate() : f.getDate())
            );
        },
        obtenerHoraActual() {
            var f = new Date();
            return (
                (f.getHours() < 10 ? "0" + f.getHours() : f.getHours()) +
                ":" +
                (f.getMinutes() < 10 ? "0" + f.getMinutes() : f.getMinutes())
            );
        },
        obtenerFechaHoraActual() {
            var f = new Date();
            let mostrar =
                (f.getDate() < 10 ? "0" + f.getDate() : f.getDate()) +
                "/" +
                (f.getMonth() + 1 < 10
                    ? "0" + (f.getMonth() + 1)
                    : f.getMonth() + 1) +
                "/" +
                f.getFullYear() +
                " " +
                (f.getHours() < 10 ? "0" + f.getHours() : f.getHours()) +
                ":" +
                (f.getMinutes() < 10 ? "0" + f.getMinutes() : f.getMinutes()) +
                ":" +
                (f.getSeconds() < 10 ? "0" + f.getSeconds() : f.getSeconds());

            let hidden =
                f.getFullYear() +
                "-" +
                (f.getMonth() + 1 < 10
                    ? "0" + (f.getMonth() + 1)
                    : f.getMonth() + 1) +
                "-" +
                (f.getDate() < 10 ? "0" + f.getDate() : f.getDate()) +
                " " +
                (f.getHours() < 10 ? "0" + f.getHours() : f.getHours()) +
                ":" +
                (f.getMinutes() < 10 ? "0" + f.getMinutes() : f.getMinutes()) +
                ":" +
                (f.getSeconds() < 10 ? "0" + f.getSeconds() : f.getSeconds());

            return { mostrar: mostrar, hidden: hidden };
        },
        agruparCelular(e) {
            // let key = e.keyCode || e.which
            // let tecla = String.fromCharCode(key).toLowerCase()
            // eslint-disable-next-line no-undef
            let valor = $('input[name="celular"]').val();
            // alert(valor + ' ' + tecla + ' ' + key)
            let nuevaCadena = "";
            let contador = 0;
            if (valor.length > 3) {
                for (let i = 0; i < valor.length; i++) {
                    if (contador === 3) {
                        nuevaCadena += " ";
                        contador = 0;
                    }

                    if (valor[i] !== " ") {
                        nuevaCadena += valor[i];
                        contador++;
                    }
                }
            }

            if (valor.length >= 9) {
                // eslint-disable-next-line no-undef
                $('input[name="celular"]').val(nuevaCadena);
            }
        },
        salir() {
            // this.$router.push('/')
            localStorage.clear();
            this.$router.replace("/");

            /* this.$store.state.autorizado = false
            this.$store.state.usuario = null
            this.$store.state.colaborador = null
            this.$store.state.menu1 = null
            this.$store.state.menu2 = null
            sessionStorage.setItem('autenticado', this.$store.state.autorizado)
            sessionStorage.setItem('usuario', this.$store.state.usuario)
            sessionStorage.setItem('colaborador', this.$store.state.colaborador)
            sessionStorage.setItem('menu1', this.$store.state.menu1)
            sessionStorage.setItem('menu2', this.$store.state.menu2) */
        },
        activarVista() {
            // eslint-disable-next-line no-undef
            // $('#mainnav-container').click()
        },
        initLogin() {
            $('[data-toggle="tooltip"]').tooltip();
            $(".preloader").fadeOut();
            $("#loginform").fadeIn();
            // ============================================================== 
            // Login and Recover Password 
            // ============================================================== 
            // $('#to-recover').on("click", function() {
            //     $("#loginform").slideUp();
            //     $("#recoverform").fadeIn();
            // });
        },
        initPages() {
            $('[data-toggle="tooltip"]').tooltip();
            $(".preloader").fadeIn();
        },
        serializeForm(form) {
            var field,
                s = {};
            if (typeof form == "object" && form.nodeName == "FORM") {
                var len = form.elements.length;
                for (let i = 0; i < len; i++) {
                    field = form.elements[i];
                    if (
                        field.name &&
                        field.type != "file" &&
                        field.type != "reset" &&
                        field.type != "submit" &&
                        field.type != "button"
                    ) {
                        if (field.type == "select-multiple") {
                            for (
                                j = form.elements[i].options.length - 1;
                                j >= 0;
                                j--
                            ) {
                                if (field.options[j].selected)
                                    s[field.name] = field.options[j].value;
                            }
                        } else if (
                            (field.type != "checkbox" &&
                                field.type != "radio") ||
                            field.checked
                        ) {
                            s[field.name] = field.value;
                        } else if (
                            (field.type == "checkbox" ||
                                field.type == "radio") &&
                            field.checked
                        ) {
                            s[field.name] = field.value;
                        }
                    }
                }
            }
            return s; //JSON.stringify(s) //s.join('&').replace(/%20/g, '+');
        },
        renderFondo(band) {
            // let tipoPage = document.getElementById('tipoPage').value
            // document.body.style.backgroundImage = "";
            // document.body.style.backgroundSize = "";
            // document.body.style.backgroundRepeat = "";
            // document.body.style.backgroundColor = "";
            // if (!band) {
            //     document.body.style.backgroundImage =
            //         "url('https://images.pexels.com/photos/210182/pexels-photo-210182.jpeg?auto=compress&cs=tinysrgb&dpr=2&h=650&w=940')";
            //     // https://images.pexels.com/photos/3815750/pexels-photo-3815750.jpeg?auto=compress&cs=tinysrgb&dpr=2&h=650&w=940
            //     document.body.style.backgroundSize = "100% 100%";
            //     document.body.style.backgroundRepeat = "no-repeat";
            //     document.body.style.overflow = "hidden";
            // } else {
            //     document.body.style.backgroundColor = "#F2F2F2";
            //     document.body.style.overflow = "scroll";
            // }
        },
        renderTabla(entidad) {
            var el = document.getElementById("loading_" + entidad);
            var el2 = document.getElementById("tabla_" + entidad);
            el.classList.remove("d-none");
            el2.classList.add("d-none");
            window.setTimeout(function () {
                el.classList.add("d-none");
                el2.classList.remove("d-none");
            }, 300);
        },
        async enviarForm(url2, entidad) {
            let me = this;
            let icon = '<i class="mdi mdi-check-bold icon-size"></i>';
            // alert(url)
            let button = document.getElementById(`btnEnvio_${entidad}`);
            button.innerHTML = icon + " Cargando...";
            button.disabled = true;
            var form = document.getElementById(`formEnv_${entidad}`);
            let response = null;
            if (entidad != 'auto-mant') {
                let data = me.serializeForm(form);
                // console.log('data',data)
                // alert(form.action+'-'+form.method)
                // let formData = new FormData(form)
                // console.log("form", form);
                // console.log('DATA',formData)
                response = await axios({
                    method: form.method,
                    url: form.action,
                    data: data,
                });
            } else {
                const formData = new FormData(form);
                response = await axios({
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    },
                    method: form.method,
                    url: form.action,
                    data: formData,
                });
            }
            var arreglo = response.data.errores;
            let cadena_errors = "";
            Object.values(arreglo).forEach((val) => {
                cadena_errors += val + ", ";
            });

            if (response.data.estado == false) {
                document.getElementById("data-error_" + entidad).innerHTML =
                    '<div style = "margin-top:10px;" > <div style="background-color: #ffdbdf !important;" class="alert alert-danger text-left alert-dismissible" role="alert"><button type = "button" class="close" data-dismiss = "alert">&times;</button><strong>Corregir los Siguientes Errores:</strong> ' +
                    cadena_errors.slice(0, -2) +
                    "</a></div ></div >";
            } else {
                document.getElementById("data-error_" + entidad).innerHTML =
                    '<div style = "margin-top:10px;"><div class="alert alert-success text-left alert-dismissible" role="alert"><button type = "button" class="close" data-dismiss="alert">&times;</button><strong>' +
                    cadena_errors.slice(0, -1) +
                    "</strong></a></div ></div>";

                if (entidad == "orden_mant") {
                    localStorage.removeItem("rptas_marcadas");
                }

                if (url2 != null) {
                    window.setTimeout(function () {
                        me.atras(url2);
                    }, 250);
                } else {
                    // window.location.reload()
                }
            }
            button.disabled = false;
            button.innerHTML = icon + " Guardar";
        },
        async enviarFormFile(url2, entidad, id_file, nombreFile) {
            let me = this;
            let icon = '<i class="mdi mdi-check-bold icon-size"></i>';
            // alert(url)
            let button = document.getElementById(`btnEnvio_${entidad}`);
            button.innerHTML = icon + " Cargando...";
            button.disabled = true;
            var form = document.getElementById(`formEnv_${entidad}`);
            let data = me.serializeForm(form);

            let file = $(`#formEnv_${entidad} :input[id="${id_file}"]`);
            const formData = new FormData();

            Object.keys(data).forEach((key) => {
                formData.append(key, data[key]);
            });
            formData.append(nombreFile, file[0].files[0]);

            // console.log('data',data)
            // alert(form.action+'-'+form.method)
            // let formData = new FormData(form)
            // console.log("form", form);
            // console.log('DATA',formData)
            let response = await axios({
                method: form.method,
                url: form.action,
                data: formData,
                headers: { "Content-Type": "multipart/form-data" },
            });

            var arreglo = response.data.errores;
            let cadena_errors = "";
            Object.values(arreglo).forEach((val) => {
                cadena_errors += val + ", ";
            });

            if (response.data.estado == false) {
                document.getElementById("data-error_" + entidad).innerHTML =
                    '<div style = "margin-top:10px;" > <div style="background-color: #ffdbdf !important;" class="alert alert-danger text-left alert-dismissible" role="alert"><button type = "button" class="close" data-dismiss = "alert">&times;</button><strong>Corregir los Siguientes Errores:</strong> ' +
                    cadena_errors.slice(0, -2) +
                    "</a></div ></div >";
            } else {
                document.getElementById("data-error_" + entidad).innerHTML =
                    '<div style = "margin-top:10px;"><div class="alert alert-success text-left alert-dismissible" role="alert"><button type = "button" class="close" data-dismiss="alert">&times;</button><strong>' +
                    cadena_errors.slice(0, -1) +
                    "</strong></a></div ></div>";

                if (url2 != null) {
                    window.setTimeout(function () {
                        me.atras(url2);
                    }, 250);
                } else {
                    // window.location.reload()
                }
            }
            button.disabled = false;
            button.innerHTML = icon + " Guardar";
        },
        async enviarFormImg(url2) {
            let me = this;
            // alert(url)
            var form = document.getElementById("formEnv");
            let data = me.serializeForm(form);

            let img = $('#formEnv :input[id="checklist"]');
            const formData = new FormData();

            Object.keys(data).forEach((key) => {
                formData.append(key, data[key]);
            });
            formData.append("imagen", img[0].files[0]);

            // console.log('data',data)
            // alert(form.action+'-'+form.method)
            // let formData = new FormData(form)
            // console.log("form", form);
            // console.log('DATA',formData)
            let response = await axios({
                method: form.method,
                url: form.action,
                data: formData,
                headers: { "Content-Type": "multipart/form-data" },
            });

            var arreglo = response.data.errores;
            let cadena_errors = "";
            Object.values(arreglo).forEach((val) => {
                cadena_errors += val + ", ";
            });

            if (response.data.estado == false) {
                document.getElementById("data-error").innerHTML =
                    '<div style = "margin-top:10px;" > <div style="background-color: #ffdbdf !important;" class="alert alert-danger text-left alert-dismissible" role="alert"><button type = "button" class="close" data-dismiss = "alert">&times;</button><strong>Corregir los Siguientes Errores:</strong> ' +
                    cadena_errors.slice(0, -2) +
                    "</a></div ></div >";
            } else {
                document.getElementById("data-error").innerHTML =
                    '<div style = "margin-top:10px;"><div class="alert alert-success text-left alert-dismissible" role="alert"><button type = "button" class="close" data-dismiss = "alert">&times;</button><strong>' +
                    cadena_errors.slice(0, -1) +
                    "</strong></a></div ></div>";

                if (url2 != null) {
                    window.setTimeout(function () {
                        me.atras(url2);
                    }, 2000);
                } else {
                    window.location.reload();
                }
            }
        },
        async enviarFormM(elemento, entidad) {
            let me = this;
            // alert(url)
            let icon = '<i class="mdi mdi-check-bold icon-size"></i>';
            // alert(url)
            let button = document.getElementById("btnEnvio_" + entidad);
            button.innerHTML = icon + " Cargando...";
            button.disabled = true;

            var form = document.getElementById("formEnv_" + entidad);
            let data = me.serializeForm(form);
            // console.log('data',data)
            // alert(form.action+'-'+form.method)
            // let formData = new FormData(form)
            // console.log("form", form);
            // console.log('DATA',formData)
            let response = await axios({
                method: form.method,
                url: form.action,
                data: data,
            });

            var arreglo = response.data.errores;
            let cadena_errors = "";
            Object.values(arreglo).forEach((val) => {
                cadena_errors += val + ", ";
            });

            if (response.data.estado == false) {
                document.getElementById("data-error_" + entidad).innerHTML =
                    '<div style = "margin-top:10px;" > <div style="background-color: #ffdbdf !important;" class="alert alert-danger text-left alert-dismissible" role="alert"><button type = "button" class="close" data-dismiss = "alert">&times;</button><strong>Corregir los Siguientes Errores:</strong> ' +
                    cadena_errors.slice(0, -2) +
                    "</a></div ></div >";
            } else {
                document.getElementById("data-error_" + entidad).innerHTML =
                    '<div style = "margin-top:10px;"><div class="alert alert-success text-left alert-dismissible" role="alert"><button type = "button" class="close" data-dismiss = "alert">&times;</button><strong>' +
                    cadena_errors.slice(0, -1) +
                    "</strong></a></div ></div>";

                if (elemento != null) {
                    if (entidad === 'mant_oportunidad') {
                        this.$parent.$emit("busquedaListProspecto");
                        this.$parent.$emit("busquedaListOportunidad");
                    }

                    window.setTimeout(function () {
                        $("#" + elemento).modal("toggle");
                        $("#" + elemento).css('z-index', '-1')
                       
                        $('#header-sec').css('z-index','')
                        $('#aside-sec').css('z-index','')  

                        $("body").removeClass("modal-open");
                        $(".modal-backdrop").remove();
                    }, 280);
                }
            }

            button.disabled = false;
            button.innerHTML = icon + " Guardar";
        },
        validarDetalles() {
            let tipo = document.getElementById("select_tipo_operacion").value;
            // console.log('estoy aqi');
            let arreglo = "";
            if (tipo == "E") {
                arreglo = document.getElementById("listProductos").value;
            } else {
                arreglo = document.getElementById("listDetalles").value;
            }

            let detalles = arreglo.split(",");
            let cadena_errors = "";
            let band = true;
            if (detalles != null && detalles != "") {
                // console.log(detalles, arreglo)
                detalles.forEach((element) => {
                    let cantidad = document.getElementById(
                        "txtcantidad" + element
                    ).value;
                    let producto = document.getElementById(
                        "txtproducto" + element
                    ).value;
                    let precio = document.getElementById(
                        "txtprecio" + element
                    ).value;
                    //    let stock   = document.getElementById('stock'+element).value

                    if (cantidad == "") {
                        cadena_errors += "Indique Cantidad en los Detalles, ";
                        document
                            .getElementById("txtcantidad" + element)
                            .focus();
                        band = false;
                    }

                    if (producto == "") {
                        cadena_errors += "Indique Producto en los Detalles, ";
                        document
                            .getElementById("txtproducto" + element)
                            .focus();
                        band = false;
                    }

                    if (precio == "") {
                        cadena_errors += "Indique Precio en los Detalles, ";
                        document.getElementById("txtprecio" + element).focus();
                        band = false;
                    }

                    if (!band) {
                        document.getElementById("data-error-modal").innerHTML =
                            '<div style = "margin-top:10px;" > <div style="background-color: #ffdbdf !important;" class="alert alert-danger text-left alert-dismissible" role="alert"><button type = "button" class="close" data-dismiss = "alert">&times;</button><strong>Corregir los Siguientes Errores:</strong> ' +
                            cadena_errors.slice(0, -2) +
                            "</a></div ></div >";
                    }

                    //    if (cantidad > stock) {
                    //         cadena_errors += 'Cantidad es Mayor a Stock en los Detalles, '
                    //         band = false
                    //    }
                });
            }

            if (detalles == "") {
                document.getElementById("data-error-modal").innerHTML =
                    '<div style = "margin-top:10px;" > <div style="background-color: #ffdbdf !important;" class="alert alert-danger text-left alert-dismissible" role="alert"><button type = "button" class="close" data-dismiss = "alert">&times;</button><strong>Corregir los Siguientes Errores:</strong> Agregue Detalles por favor</a></div ></div >';
                band = false;
            }

            return band;
        },
        validarDetallesComprasInv(entidad) {
            let cadena_errors = "";
            let band = true;

            let arreglo3 = document.getElementById(
                `listProductos_${entidad}`
            ).value;
            let detalles3 = arreglo3.split(",");

            if (detalles3 != null && detalles3 != "") {
                detalles3.forEach((element) => {
                    let cantidad = document.getElementById(
                        "txtcantidad" + element
                    ).value;
                    let producto = document.getElementById(
                        "txtproducto" + element
                    ).value;
                    let precio = document.getElementById(
                        "txtprecio" + element
                    ).value;
                    let pv = document.getElementById(
                        "txtprecioventa" + element
                    ).value;

                    if (cantidad == "") {
                        cadena_errors += "Indique Cantidad en los Detalles, ";
                        document
                            .getElementById("txtcantidad" + element)
                            .focus();
                        band = false;
                    }

                    if (producto == "") {
                        cadena_errors += "Indique Producto en los Detalles, ";
                        document
                            .getElementById("txtproducto" + element)
                            .focus();
                        band = false;
                    }

                    if (precio == "") {
                        cadena_errors +=
                            "Indique Precio de Compra en los Detalles, ";
                        document.getElementById("txtprecio" + element).focus();
                        band = false;
                    }

                    if (pv == "") {
                        cadena_errors +=
                            "Indique Precio de Venta en los Detalles, ";
                        document
                            .getElementById("txtprecioventa" + element)
                            .focus();
                        band = false;
                    }

                    if (!band) {
                        document.getElementById(
                            "data-error-modal_" + entidad
                        ).innerHTML =
                            '<div style = "margin-top:10px;" > <div style="background-color: #ffdbdf !important;" class="alert alert-danger text-left alert-dismissible" role="alert"><button type = "button" class="close" data-dismiss = "alert">&times;</button><strong>Corregir los Siguientes Errores:</strong> ' +
                            cadena_errors.slice(0, -2) +
                            "</a></div ></div >";
                    }
                });
            }

            if (detalles3 == "") {
                document.getElementById(
                    "data-error-modal_" + entidad
                ).innerHTML =
                    '<div style = "margin-top:10px;" > <div style="background-color: #ffdbdf !important;" class="alert alert-danger text-left alert-dismissible" role="alert"><button type = "button" class="close" data-dismiss = "alert">&times;</button><strong>Corregir los Siguientes Errores:</strong> Agregue Detalles por favor</a></div ></div >';
                band = false;
            }

            return band;
        },
        validarDetallesBaja(entidad) {
            let cadena_errors = "";
            let band = true;

            let arreglo3 = document.getElementById(
                `listDetalles_${entidad}`
            ).value;
            let detalles3 = arreglo3.split(",");

            if (detalles3 != null && detalles3 != "") {
                detalles3.forEach((element) => {
                    let elemCantidad = document.getElementById(
                        "cantidad_" + element
                    );
                    let cantidad = elemCantidad.value;
                    let stockMax = elemCantidad.max;
                    // console.log(`element: ${element}, cantidad: ${cantidad}, stock: ${stockMax}`)
                    let producto = document.getElementById(
                        "descripcion_" + element
                    ).value;
                    let precio = document.getElementById(
                        "preciounit_" + element
                    ).value;

                    if (cantidad == "") {
                        cadena_errors += "Indique Cantidad en los Detalles, ";
                        document.getElementById("cantidad_" + element).focus();
                        band = false;
                    }

                    if (parseFloat(cantidad) > parseFloat(stockMax)) {
                        cadena_errors +=
                            "Cantidad Indicada, supera al stock en los Detalles, ";
                        document.getElementById("cantidad_" + element).focus();
                        band = false;
                    }

                    if (producto == "") {
                        cadena_errors +=
                            "Indique Descripción en los Detalles, ";
                        document
                            .getElementById("descripcion_" + element)
                            .focus();
                        band = false;
                    }

                    if (precio == "") {
                        cadena_errors += "Indique Precio en los Detalles, ";
                        document
                            .getElementById("preciounit_" + element)
                            .focus();
                        band = false;
                    }

                    if (!band) {
                        document.getElementById(
                            "data-error_" + entidad
                        ).innerHTML =
                            '<div style = "margin-top:10px;" > <div style="background-color: #ffdbdf !important;" class="alert alert-danger text-left alert-dismissible" role="alert"><button type = "button" class="close" data-dismiss = "alert">&times;</button><strong>Corregir los Siguientes Errores:</strong> ' +
                            cadena_errors.slice(0, -2) +
                            "</a></div ></div>";
                    }
                });
            }

            if (detalles3 == "") {
                document.getElementById("data-error_" + entidad).innerHTML =
                    '<div style = "margin-top:10px;" > <div style="background-color: #ffdbdf !important;" class="alert alert-danger text-left alert-dismissible" role="alert"><button type = "button" class="close" data-dismiss = "alert">&times;</button><strong>Corregir los Siguientes Errores:</strong> Agregue Detalles por favor</a></div ></div >';
                band = false;
            }

            return band;
        },
        validarDetallesCompras(entidad) {
            let cadena_errors = "";
            let band = true;

            let arreglo3 = document.getElementById(
                `listProductos_${entidad}`
            ).value;
            let detalles3 = arreglo3.split(",");

            if (detalles3 != null && detalles3 != "") {
                detalles3.forEach((element) => {
                    let cantidad = document.getElementById(
                        "txtcantidad" + element
                    ).value;
                    let producto = document.getElementById(
                        "txtproducto" + element
                    ).value;
                    let precio = document.getElementById(
                        "txtprecio" + element
                    ).value;
                    let pv = document.getElementById(
                        "txtprecioventa" + element
                    ).value;

                    if (cantidad == "") {
                        cadena_errors += "Indique Cantidad en los Detalles, ";
                        document
                            .getElementById("txtcantidad" + element)
                            .focus();
                        band = false;
                    }

                    if (producto == "") {
                        cadena_errors += "Indique Producto en los Detalles, ";
                        document
                            .getElementById("txtproducto" + element)
                            .focus();
                        band = false;
                    }

                    if (precio == "") {
                        cadena_errors +=
                            "Indique Precio de Compra en los Detalles, ";
                        document.getElementById("txtprecio" + element).focus();
                        band = false;
                    }

                    if (pv == "") {
                        cadena_errors +=
                            "Indique Precio de Venta en los Detalles, ";
                        document
                            .getElementById("txtprecioventa" + element)
                            .focus();
                        band = false;
                    }

                    if (!band) {
                        document.getElementById(
                            "data-error_" + entidad
                        ).innerHTML =
                            '<div style = "margin-top:10px;" > <div style="background-color: #ffdbdf !important;" class="alert alert-danger text-left alert-dismissible" role="alert"><button type = "button" class="close" data-dismiss = "alert">&times;</button><strong>Corregir los Siguientes Errores:</strong> ' +
                            cadena_errors.slice(0, -2) +
                            "</a></div ></div >";
                    }
                });
            }

            if (detalles3 == "") {
                document.getElementById("data-error_" + entidad).innerHTML =
                    '<div style = "margin-top:10px;" > <div style="background-color: #ffdbdf !important;" class="alert alert-danger text-left alert-dismissible" role="alert"><button type = "button" class="close" data-dismiss = "alert">&times;</button><strong>Corregir los Siguientes Errores:</strong> Agregue Detalles por favor</a></div ></div >';
                band = false;
            }

            return band;
        },
        validarDetallePedidoCompra (entidad) {
            let cadena_errors = "";
            let band = true;

            let arreglo3 = document.getElementById(
                `listProductos_${entidad}`
            ).value;
            let arreglo4 = document.getElementById(
                `listServicios_${entidad}`
            ).value;
            let detalles3 = arreglo3.split(",").concat(arreglo4.split(","));

            if (detalles3 != null && detalles3 != "") {
                detalles3.forEach((element) => {
                    console.log('el', element);
                    if (element != "") {
                        let cantidad = document.getElementById(
                            "txtcantidad" + element
                        ).value;
                        let producto = document.getElementById(
                            "txtproducto" + element
                        ).value;
                        let precio = document.getElementById(
                            "txtprecio" + element
                        ).value;
                        // let pv = document.getElementById(
                        //     "txtprecioventa" + element
                        // ).value;

                        if (cantidad == "") {
                            cadena_errors += "Indique Cantidad en los Detalles, ";
                            document
                                .getElementById("txtcantidad" + element)
                                .focus();
                            band = false;
                        }

                        if (producto == "") {
                            cadena_errors += "Indique Producto/Servicio en los Detalles, ";
                            document
                                .getElementById("txtproducto" + element)
                                .focus();
                            band = false;
                        }

                        if (precio == "") {
                            cadena_errors +=
                                "Indique Precio en los Detalles, ";
                            document.getElementById("txtprecio" + element).focus();
                            band = false;
                        }

                        // if (pv == "") {
                        //     cadena_errors +=
                        //         "Indique Precio de Venta en los Detalles, ";
                        //     document
                        //         .getElementById("txtprecioventa" + element)
                        //         .focus();
                        //     band = false;
                        // }

                        if (!band) {
                            document.getElementById(
                                "data-error_" + entidad
                            ).innerHTML =
                                '<div style = "margin-top:10px;" > <div style="background-color: #ffdbdf !important;" class="alert alert-danger text-left alert-dismissible" role="alert"><button type = "button" class="close" data-dismiss = "alert">&times;</button><strong>Corregir los Siguientes Errores:</strong> ' +
                                cadena_errors.slice(0, -2) +
                                "</a></div ></div >";
                        }
                    }
                });
            }

            if (detalles3 == "") {
                document.getElementById("data-error_" + entidad).innerHTML =
                    '<div style = "margin-top:10px;" > <div style="background-color: #ffdbdf !important;" class="alert alert-danger text-left alert-dismissible" role="alert"><button type = "button" class="close" data-dismiss = "alert">&times;</button><strong>Corregir los Siguientes Errores:</strong> Agregue Detalles por favor</a></div ></div >';
                band = false;
            }

            return band;
        },
        validarDetalleCompraAuto(entidad) {
            let cadena_errors = "";
            let band = true;

            let arreglo3 = document.getElementById(
                `listAutos_${entidad}`
            ).value;
            let detalles3 = arreglo3.split(",");

            if (detalles3 != null && detalles3 != "") {
                detalles3.forEach((element) => {
                    let cantidad = document.getElementById(
                        "txtcantidad" + element
                    ).value;
                    let descripcion_adic = document.getElementById(
                        "descripcion_adic" + element
                    ).value;
                    let producto = document.getElementById(
                        "txtproducto" + element
                    ).value;
                    let precio = document.getElementById(
                        "txtprecio" + element
                    ).value;
                    let pv = document.getElementById(
                        "txtprecioventa" + element
                    ).value;

                    if (cantidad == "") {
                        cadena_errors += "Indique Cantidad en los Detalles, ";
                        document
                            .getElementById("txtcantidad" + element)
                            .focus();
                        band = false;
                    }
                    if (descripcion_adic == "") {
                        cadena_errors +=
                            "Indique Descripción Adicional en los Detalles, ";
                        document
                            .getElementById("descripcion_adic" + element)
                            .focus();
                        band = false;
                    }

                    if (producto == "") {
                        cadena_errors += "Indique Producto en los Detalles, ";
                        document
                            .getElementById("txtproducto" + element)
                            .focus();
                        band = false;
                    }

                    if (precio == "") {
                        cadena_errors +=
                            "Indique Precio de Compra en los Detalles, ";
                        document.getElementById("txtprecio" + element).focus();
                        band = false;
                    }

                    if (pv == "") {
                        cadena_errors +=
                            "Indique Precio de Venta en los Detalles, ";
                        document
                            .getElementById("txtprecioventa" + element)
                            .focus();
                        band = false;
                    }

                    if (!band) {
                        document.getElementById(
                            `data-error_${entidad}`
                        ).innerHTML =
                            '<div style = "margin-top:10px;" > <div style="background-color: #ffdbdf !important;" class="alert alert-danger text-left alert-dismissible" role="alert"><button type = "button" class="close" data-dismiss = "alert">&times;</button><strong>Corregir los Siguientes Errores:</strong> ' +
                            cadena_errors.slice(0, -2) +
                            "</a></div ></div >";
                    }
                });
            }

            if (detalles3 == "") {
                document.getElementById(`data-error_${entidad}`).innerHTML =
                    '<div style = "margin-top:10px;" > <div style="background-color: #ffdbdf !important;" class="alert alert-danger text-left alert-dismissible" role="alert"><button type = "button" class="close" data-dismiss = "alert">&times;</button><strong>Corregir los Siguientes Errores:</strong> Agregue Detalles por favor</a></div ></div >';
                band = false;
            }

            return band;
        },
        validarDetalleCotizacionAuto(entidad) {
            let cadena_errors = "";
            let band = true;

            let arreglo3 = document.getElementById(
                `listDetalles_${entidad}`
            ).value;
            let detalles3 = arreglo3.split(",");

            if (detalles3 != null && detalles3 != "") {
                detalles3.forEach((element) => {
                    let cantidad = document.getElementById(
                        "txtcantidad" + element
                    ).value;
                    let producto = document.getElementById(
                        "txtproducto" + element
                    ).value;
                    let precio = document.getElementById(
                        "txtprecio" + element
                    ).value;

                    if (cantidad == "") {
                        cadena_errors += "Indique Cantidad en los Detalles, ";
                        document
                            .getElementById("txtcantidad" + element)
                            .focus();
                        band = false;
                    }

                    if (producto == "") {
                        cadena_errors += "Indique Producto en los Detalles, ";
                        document
                            .getElementById("txtproducto" + element)
                            .focus();
                        band = false;
                    }

                    if (precio == "") {
                        cadena_errors +=
                            "Indique Precio de Compra en los Detalles, ";
                        document.getElementById("txtprecio" + element).focus();
                        band = false;
                    }

                    if (!band) {
                        document.getElementById(
                            `data-error_${entidad}`
                        ).innerHTML =
                            '<div style = "margin-top:10px;" > <div style="background-color: #ffdbdf !important;" class="alert alert-danger text-left alert-dismissible" role="alert"><button type = "button" class="close" data-dismiss = "alert">&times;</button><strong>Corregir los Siguientes Errores:</strong> ' +
                            cadena_errors.slice(0, -2) +
                            "</a></div ></div >";
                    }
                });
            }

            if (detalles3 == "") {
                document.getElementById(`data-error_${entidad}`).innerHTML =
                    '<div style = "margin-top:10px;" > <div style="background-color: #ffdbdf !important;" class="alert alert-danger text-left alert-dismissible" role="alert"><button type = "button" class="close" data-dismiss = "alert">&times;</button><strong>Corregir los Siguientes Errores:</strong> Agregue Detalles por favor</a></div ></div >';
                band = false;
            }

            return band;
        },
        validarDetalleCotizacion(entidad) {
            let cadena_errors = "";
            let band = true;

            let arreglo3 = document.getElementById(
                `listDetalles_${entidad}`
            ).value;
            let detalles3 = arreglo3.split(",");

            if (detalles3 != null && detalles3 != "") {
                detalles3.forEach((element) => {
                    let cantidad = document.getElementById(
                        "txtcantidad" + element
                    ).value;
                    let producto = document.getElementById(
                        "txtproducto" + element
                    ).value;
                    let precio = document.getElementById(
                        "txtprecio" + element
                    ).value;
                    let precioDesc = document.getElementById("txtdescuentounit"+element)
                    // let tipo     = document.getElementById('tipo'+ element).value
                    // let stock    = document.getElementById('stock'+ element).value

                    if (cantidad == "") {
                        cadena_errors += "Indique Cantidad en los Detalles, ";
                        document
                            .getElementById("txtcantidad" + element)
                            .focus();
                        band = false;
                    }

                    if (producto == "") {
                        cadena_errors += "Indique Producto en los Detalles, ";
                        document
                            .getElementById("txtproducto" + element)
                            .focus();
                        band = false;
                    }

                    if (precio == "") {
                        cadena_errors +=
                            "Indique Precio en los Detalles, ";
                        document.getElementById("txtprecio" + element).focus();
                        band = false;
                    }

                    if (precioDesc != null) {
                        let valPrecioDec = precioDesc.value
                        if (valPrecioDec == "") {
                            cadena_errors +=
                                "Indique Precio Desc en los Detalles, ";
                            document.getElementById("txtdescuentounit" + element).focus();
                            band = false;
                        }
                    }
                    // if (tipo == 'Producto') {
                    //     cadena_errors += 'Indique Precio de Compra en los Detalles, '
                    //     document.getElementById('txtprecio' + element).focus()
                    // }

                    if (!band) {
                        document.getElementById(
                            `data-error_${entidad}`
                        ).innerHTML =
                            '<div style = "margin-top:10px;" > <div style="background-color: #ffdbdf !important;" class="alert alert-danger text-left alert-dismissible" role="alert"><button type = "button" class="close" data-dismiss = "alert">&times;</button><strong>Corregir los Siguientes Errores:</strong> ' +
                            cadena_errors.slice(0, -2) +
                            "</a></div ></div >";
                    }
                });
            }

            if (detalles3 == "") {
                document.getElementById(`data-error_${entidad}`).innerHTML =
                    '<div style = "margin-top:10px;" > <div style="background-color: #ffdbdf !important;" class="alert alert-danger text-left alert-dismissible" role="alert"><button type = "button" class="close" data-dismiss = "alert">&times;</button><strong>Corregir los Siguientes Errores:</strong> Agregue Detalles por favor</a></div ></div >';
                band = false;
            }

            return band;
        },
        validarDetallePaquete(entidad) {
            let cadena_errors = "";
            let band = true;

            let arreglo3 = document.getElementById(
                `listDetalles_${entidad}`
            ).value;
            let detalles3 = arreglo3.split(",");

            if (detalles3 != null && detalles3 != "") {
                detalles3.forEach((element) => {
                    let cantidad = document.getElementById(
                        "txtcantidad" + element
                    ).value;
                    let producto = document.getElementById(
                        "txtproducto" + element
                    ).value;
                    let precio = document.getElementById(
                        "txtprecio" + element
                    ).value;
                    // let tipo     = document.getElementById('tipo'+ element).value
                    // let stock    = document.getElementById('stock'+ element).value

                    if (cantidad == "") {
                        cadena_errors += "Indique Cantidad en los Detalles, ";
                        document
                            .getElementById("txtcantidad" + element)
                            .focus();
                        band = false;
                    }

                    if (producto == "") {
                        cadena_errors += "Indique Producto en los Detalles, ";
                        document
                            .getElementById("txtproducto" + element)
                            .focus();
                        band = false;
                    }

                    if (precio == "") {
                        cadena_errors += "Indique Precio en los Detalles, ";
                        document.getElementById("txtprecio" + element).focus();
                        band = false;
                    }

                    // if (tipo == 'Producto') {
                    //     cadena_errors += 'Indique Precio de Compra en los Detalles, '
                    //     document.getElementById('txtprecio' + element).focus()
                    // }

                    if (!band) {
                        document.getElementById(
                            `data-error_${entidad}`
                        ).innerHTML =
                            '<div style = "margin-top:10px;" > <div style="background-color: #ffdbdf !important;" class="alert alert-danger text-left alert-dismissible" role="alert"><button type = "button" class="close" data-dismiss = "alert">&times;</button><strong>Corregir los Siguientes Errores:</strong> ' +
                            cadena_errors.slice(0, -2) +
                            "</a></div ></div >";
                    }
                });
            }

            if (detalles3 == "") {
                document.getElementById(`data-error_${entidad}`).innerHTML =
                    '<div style = "margin-top:10px;" > <div style="background-color: #ffdbdf !important;" class="alert alert-danger text-left alert-dismissible" role="alert"><button type = "button" class="close" data-dismiss = "alert">&times;</button><strong>Corregir los Siguientes Errores:</strong> Agregue Detalles por favor</a></div ></div >';
                band = false;
            }

            return band;
        },
        async enviarFormModal(elemento) {
            let me = this;
            let bandera = me.validarDetalles();
            if (bandera) {
                // alert(url)
                var form = document.getElementById("formEnvModal");
                let data = me.serializeForm(form);
                // console.log('data',data)
                // alert(form.action+'-'+form.method)
                // let formData = new FormData(form)
                // console.log("form", form);
                // console.log('DATA',formData)
                let response = await axios({
                    method: form.method,
                    url: form.action,
                    data: data,
                });

                var arreglo = response.data.errores;
                let cadena_errors = "";
                Object.values(arreglo).forEach((val) => {
                    cadena_errors += val + ", ";
                });

                if (response.data.estado == false) {
                    document.getElementById("data-error-modal").innerHTML =
                        '<div style = "margin-top:10px;" > <div style="background-color: #ffdbdf !important;" class="alert alert-danger text-left alert-dismissible" role="alert"><button type = "button" class="close" data-dismiss = "alert">&times;</button><strong>Corregir los Siguientes Errores:</strong> ' +
                        cadena_errors.slice(0, -2) +
                        "</a></div ></div >";
                } else {
                    document.getElementById("data-error-modal").innerHTML =
                        '<div style = "margin-top:10px;"><div class="alert alert-success text-left alert-dismissible" role="alert"><button type = "button" class="close" data-dismiss = "alert">&times;</button><strong>' +
                        cadena_errors.slice(0, -2) +
                        "</strong></a></div ></div>";

                    if (elemento != "" && elemento != null) {
                        window.setTimeout(function () {
                            $("#" + elemento).modal("hide");
                            $("body").removeClass("modal-open");
                            $(".modal-backdrop").remove();
                        }, 1000);
                    } else {
                        let url2 = "/venta";
                        window.setTimeout(function () {
                            me.atras(url2);
                        }, 1000);

                        if (response.data.id != 0) {
                            miVentana = window.open(
                                "https://fasteinvoice.com/consultar.php?ruc=20603144954&password=valentinasunat123&id=" +
                                    response.data.id,
                                "Impresión de Comprobante Electrónico",
                                "scrollbars=1,status=0,titlebar=0,toolbar=0,resizable=1,width=1200, height=800"
                            );
                            miVentana.print();
                        }
                    }
                }
            }
        },
        async enviarFormModalCompra(urlE) {
            let me = this;
            let bandera = me.validarDetallesCompras();
            if (bandera) {
                var form = document.getElementById("formEnvModal");
                let data = me.serializeForm(form);

                let response = await axios({
                    method: form.method,
                    url: form.action,
                    data: data,
                });

                var arreglo = response.data.errores;
                let cadena_errors = "";
                Object.values(arreglo).forEach((val) => {
                    cadena_errors += val + ", ";
                });

                if (response.data.estado == false) {
                    document.getElementById("data-error-modal").innerHTML =
                        '<div style = "margin-top:10px;" > <div style="background-color: #ffdbdf !important;" class="alert alert-danger text-left alert-dismissible" role="alert"><button type = "button" class="close" data-dismiss = "alert">&times;</button><strong>Corregir los Siguientes Errores:</strong> ' +
                        cadena_errors.slice(0, -2) +
                        "</a></div></div>";
                } else {
                    document.getElementById("data-error-modal").innerHTML =
                        '<div style = "margin-top:10px;"><div class="alert alert-success text-left alert-dismissible" role="alert"><button type = "button" class="close" data-dismiss = "alert">&times;</button><strong>' +
                        cadena_errors.slice(0, -2) +
                        "</strong></a></div ></div>";

                    window.setTimeout(function () {
                        me.atras(urlE);
                    }, 1000);
                }
            }
        },
        async enviarFormDetalles(urlE, entidad, validacion) {
            let me = this;
            let icon = '<i class="mdi mdi-check-bold icon-size"></i>';
            // alert(url)
            let button = document.getElementById(`btnEnvio_${entidad}`);
            button.innerHTML = icon + " Cargando...";
            button.disabled = true;

            let bandera = false;
            if (validacion == "C") {
                bandera = me.validarDetallesCompras(entidad);
            } else if (validacion == "CA") {
                bandera = me.validarDetalleCompraAuto(entidad);
            } else if (validacion == "COTA") {
                bandera = me.validarDetalleCotizacionAuto(entidad);
            } else if (validacion == "COT") {
                bandera = me.validarDetalleCotizacion(entidad);
            } else if (validacion == "PAQ") {
                bandera = me.validarDetallePaquete(entidad);
            } else if (validacion == "PC") {
                bandera = me.validarDetallePedidoCompra(entidad);
            } else {
                bandera = me.validarDetalleCotizacion(entidad);
            }

            if (bandera) {
                var form = document.getElementById(`formEnv_${entidad}`);
                let data = me.serializeForm(form);

                let response = await axios({
                    method: form.method,
                    url: form.action,
                    data: data,
                });

                var arreglo = response.data.errores;
                let cadena_errors = "";
                Object.values(arreglo).forEach((val) => {
                    cadena_errors += val + ", ";
                });

                if (response.data.estado == false) {
                    document.getElementById(`data-error_${entidad}`).innerHTML =
                        '<div style = "margin-top:10px;" > <div style="background-color: #ffdbdf !important;" class="alert alert-danger text-left alert-dismissible" role="alert"><button type = "button" class="close" data-dismiss = "alert">&times;</button><strong>Corregir los Siguientes Errores:</strong> ' +
                        cadena_errors.slice(0, -2) +
                        "</a></div></div>";
                } else {
                    document.getElementById(`data-error_${entidad}`).innerHTML =
                        '<div style = "margin-top:10px;"><div class="alert alert-success text-left alert-dismissible" role="alert"><button type = "button" class="close" data-dismiss = "alert">&times;</button><strong>' +
                        cadena_errors.slice(0, -1) +
                        "</strong></a></div ></div>";
                    if (urlE != null) {
                        if (validacion == "VENT") {
                            window.open(
                                "https://fasteinvoice.com/consultar.php?ruc=20103327378&password=bj1R8xkhHB&id=" +
                                    response.data.id,
                                "Impresión de Comprobante Electrónico",
                                "scrollbars=1,status=0,titlebar=0,toolbar=0,resizable=1,width=1200, height=800"
                            );
                        }

                        window.setTimeout(function () {
                            me.atras(urlE);
                        }, 250);
                    } else {
                        // window.location.reload()
                    }
                }
            }
            button.disabled = false;
            button.innerHTML = icon + " Guardar";
        },
        async enviarFormDetallesBaja(urlEntidad, entidad, urlDerirect) {
            let me = this;
            let icon = '<i class="mdi mdi-check-bold icon-size"></i>';
            // alert(url)
            let button = document.getElementById(`btnEnvio_${entidad}`);
            button.innerHTML = icon + " Cargando...";
            button.disabled = true;

            let bandera = me.validarDetallesBaja(entidad);

            if (bandera) {
                var form = document.getElementById(`formEnv_${entidad}`);
                let data = me.serializeForm(form);

                let response = await axios({
                    method: form.method,
                    url: urlEntidad,
                    data: data,
                });

                var arreglo = response.data.errores;
                let cadena_errors = "";
                Object.values(arreglo).forEach((val) => {
                    cadena_errors += val + ", ";
                });

                if (response.data.estado == false) {
                    document.getElementById(`data-error_${entidad}`).innerHTML =
                        '<div style = "margin-top:10px;" > <div style="background-color: #ffdbdf !important;" class="alert alert-danger text-left alert-dismissible" role="alert"><button type = "button" class="close" data-dismiss = "alert">&times;</button><strong>Corregir los Siguientes Errores:</strong> ' +
                        cadena_errors.slice(0, -2) +
                        "</a></div></div>";
                } else {
                    document.getElementById(`data-error_${entidad}`).innerHTML =
                        '<div style = "margin-top:10px;"><div class="alert alert-success text-left alert-dismissible" role="alert"><button type = "button" class="close" data-dismiss = "alert">&times;</button><strong>' +
                        cadena_errors.slice(0, -1) +
                        "</strong></a></div ></div>";
                    if (urlDerirect != null) {
                        if (response.data.id > 0) {
                            window.open(
                                "https://fasteinvoice.com/consultar.php?ruc=20103327378&password=bj1R8xkhHB&id=" +
                                    response.data.id,
                                "Impresión de Comprobante Electrónico",
                                "scrollbars=1,status=0,titlebar=0,toolbar=0,resizable=1,width=1200, height=800"
                            );
                        }
                        window.setTimeout(function () {
                            me.atras(urlDerirect);
                        }, 250);
                    } else {
                        // window.location.reload()
                    }
                }
            }
            button.disabled = false;
            button.innerHTML = icon + " Guardar";
        },
        async enviarFormModalGestion(elemento, entidad, tipo) {
            let me = this;
            var form = document.getElementById("formEnvModal_" + entidad);
            let data = me.serializeForm(form);

            let icon = '<i class="mdi mdi-check-bold icon-size"></i>';
            let button = document.getElementById("btnEnvio_" + entidad);
            button.innerHTML = icon + " Cargando...";
            button.disabled = true;

            let response = await axios({
                method: form.method,
                url: form.action,
                data: data,
            });

            var arreglo = response.data.errores;
            let cadena_errors = "";
            Object.values(arreglo).forEach((val) => {
                cadena_errors += val + ", ";
            });

            if (response.data.estado == false) {
                document.getElementById(
                    "data-error-modal_" + entidad
                ).innerHTML =
                    '<div style = "margin-top:10px;" > <div style="background-color: #ffdbdf !important;" class="alert alert-danger text-left alert-dismissible" role="alert"><button type = "button" class="close" data-dismiss = "alert">&times;</button><strong>Corregir los Siguientes Errores:</strong> ' +
                    cadena_errors.slice(0, -2) +
                    "</a></div ></div >";
            } else {
                document.getElementById(
                    "data-error-modal_" + entidad
                ).innerHTML =
                    '<div style = "margin-top:10px;"><div class="alert alert-success text-left alert-dismissible" role="alert"><button type = "button" class="close" data-dismiss = "alert">&times;</button><strong>' +
                    cadena_errors.slice(0, -2) +
                    "</strong></a></div ></div>";

                if (elemento != "" && elemento != null) {
                    if (tipo == 1) {
                        this.$parent.$emit("buscarServicio");
                    } else {
                        this.$parent.$emit("buscarGanancia");
                    }
                    window.setTimeout(function () {
                        // $("#" + elemento).modal("hide");
                        // $("body").removeClass("modal-open");
                        // $(".modal-backdrop").remove();

                        $("#" + elemento).modal("toggle");
                        $("#" + elemento).css('z-index', '-1')
                        $("body").removeClass("modal-open");
                        $(".modal-backdrop").remove();
                        $('#header-sec').css('z-index','')
                        $('#aside-sec').css('z-index','')    
                    }, 1000);
                }
            }

            button.innerHTML = icon + " Guardar";
            button.disabled = false;
        },
        async enviarFormModalAdicional(elemento, entidad) {
            let me = this;
            var form = document.getElementById("formEnvModal_" + entidad);
            let data = me.serializeForm(form);

            let icon = '<i class="mdi mdi-check-bold icon-size"></i>';
            let button = document.getElementById("btnEnvio_" + entidad);
            button.innerHTML = icon + " Cargando...";
            button.disabled = true;

            let response = await axios({
                method: form.method,
                url: form.action,
                data: data,
            });

            var arreglo = response.data.errores;
            let cadena_errors = "";
            Object.values(arreglo).forEach((val) => {
                cadena_errors += val + ", ";
            });

            if (response.data.estado == false) {
                document.getElementById(
                    "data-error-modal_" + entidad
                ).innerHTML =
                    '<div style = "margin-top:10px;" > <div style="background-color: #ffdbdf !important;" class="alert alert-danger text-left alert-dismissible" role="alert"><button type = "button" class="close" data-dismiss = "alert">&times;</button><strong>Corregir los Siguientes Errores:</strong> ' +
                    cadena_errors.slice(0, -2) +
                    "</a></div ></div >";
            } else {
                document.getElementById(
                    "data-error-modal_" + entidad
                ).innerHTML =
                    '<div style = "margin-top:10px;"><div class="alert alert-success text-left alert-dismissible" role="alert"><button type = "button" class="close" data-dismiss = "alert">&times;</button><strong>' +
                    cadena_errors.slice(0, -2) +
                    "</strong></a></div ></div>";

                if (elemento != "" && elemento != null) {
                    this.$parent.$emit("buscarAdicional");
                    window.setTimeout(function () {
                        $("#" + elemento).modal("toggle");
                        $("#" + elemento).css('z-index', '-1')
                        $("body").removeClass("modal-open");
                        $(".modal-backdrop").remove();
                        $('#header-sec').css('z-index','')
                        $('#aside-sec').css('z-index','')      
                    }, 1000);
                }
            }

            button.innerHTML = icon + " Guardar";
            button.disabled = false;
        },
        validarDetallesIncidencia (error) {
            let cadena_errors = "";
            let band = true;

            let arreglo3 = document.getElementById(
                `listDetalles`
            ).value;
            let detalles3 = arreglo3.split(",");

            if (detalles3 != null && detalles3 != "") {
                detalles3.forEach((element, index) => {
                    let retornaBD = document.getElementById(`retornaBD_${element}`).value
                    if (retornaBD == '0') {
                        let motivoId = document.getElementById(
                            "motivoId_" + element
                        );
                        let descripcion = document.getElementById(
                            "descripcion_" + element
                        ).value;
                        let estado = document.getElementById(
                            "estado_" + element
                        ).value;

                        // let tiempo = document.getElementById(
                        //     "tiempo_" + element
                        // ).value;

                        // let tipoTiempo = document.getElementById(
                        //     "tipoTiempo_" + element
                        // ).value;

                        if (motivoId == "") {
                            cadena_errors += `Indique Tipo de Incidencia en item ${index+1}, `;
                            document.getElementById("motivoId_" + element).focus();
                            band = false;
                        }

                        if (descripcion == "") {
                            cadena_errors +=
                                `Indique Descripción en Item ${index+1}, `;
                            document.getElementById("descripcion_" + element).focus();
                            band = false;
                        }

                        // if (tiempo == "") {
                        //     cadena_errors += `Indique Tiempo en Item ${index+1}, `;
                        //     document
                        //         .getElementById("tiempo_" + element)
                        //         .focus();
                        //     band = false;
                        // }

                        // if (tiempo != "" && tiempo < 0) {
                        //     cadena_errors += `Tiempo no admitido en Item ${index+1}, `;
                        //     document
                        //         .getElementById("tiempo_" + element)
                        //         .focus();
                        //     band = false;

                        // }

                        
                        if (estado == "") {
                            cadena_errors += `Indique Estado en Item ${index +1}, `;
                            document.getElementById("estado_" + element).focus();
                            band = false;
                        }

                        if (!band) {
                            document.getElementById(`${error}`).innerHTML =
                                '<div style = "margin-top:10px;" > <div style="background-color: #ffdbdf !important;" class="alert alert-danger text-left alert-dismissible" role="alert"><button type = "button" class="close" data-dismiss = "alert">&times;</button><strong>Corregir los Siguientes Errores:</strong> ' +
                                cadena_errors.slice(0, -2) +
                                "</a></div ></div>";
                        }
                    }
                });
            }

            if (detalles3 == "") {
                document.getElementById(`${error}`).innerHTML =
                    '<div style = "margin-top:10px;" > <div style="background-color: #ffdbdf !important;" class="alert alert-danger text-left alert-dismissible" role="alert"><button type = "button" class="close" data-dismiss = "alert">&times;</button><strong>Corregir los Siguientes Errores:</strong> Agregue Detalles por favor</a></div ></div >';
                band = false;
            }

            return band;
        },
        async enviarFormModalIncidencias (form, modal, error) {
            let me = this;
            let icon = '<i class="mdi mdi-check-bold icon-size"></i>';
            // alert(url)
            let button = document.getElementById(`btnEnvioG`);
            button.innerHTML = icon + " Cargando...";
            button.disabled = true;

            let bandera = me.validarDetallesIncidencia(error);

            if (bandera) {
                var formD = document.getElementById(`${form}`);
                let data = me.serializeForm(formD);

                let response = await axios({
                    method: formD.method,
                    url: formD.action,
                    data: data,
                });

                var arreglo = response.data.errores;
                let cadena_errors = "";
                Object.values(arreglo).forEach((val) => {
                    cadena_errors += val + ", ";
                });

                if (response.data.estado == false) {
                    document.getElementById(`${error}`).innerHTML =
                        '<div style = "margin-top:10px;" > <div style="background-color: #ffdbdf !important;" class="alert alert-danger text-left alert-dismissible" role="alert"><button type = "button" class="close" data-dismiss = "alert">&times;</button><strong>Corregir los Siguientes Errores:</strong> ' +
                        cadena_errors.slice(0, -2) +
                        "</a></div></div>";
                } else {
                    document.getElementById(`${error}`).innerHTML =
                        '<div style = "margin-top:10px;"><div class="alert alert-success text-left alert-dismissible" role="alert"><button type = "button" class="close" data-dismiss = "alert">&times;</button><strong>' +
                        cadena_errors.slice(0, -1) +
                        "</strong></a></div ></div>";
                  
                        window.setTimeout(function () {
                            // $("#" + modal).modal("hide");
                            // $("body").removeClass("modal-open");
                            // $(".modal-backdrop").remove();
                            $('#'+modal).modal('toggle')
                            $('#'+modal).css('z-index', '-1')
                            $('#header-sec').css('z-index','')
                            $('#aside-sec').css('z-index','')       
                        }, 1000);
                  
                }
            }
            button.disabled = false;
            button.innerHTML = icon + " Guardar";
       
        },
        async enviarFormModalOrden(elemento, eId, error) {
            let me = this;
            let icon = '<i class="mdi mdi-check-bold icon-size"></i>';
            let button = document.getElementById("btnEnvioG");
            button.innerHTML = icon + " Cargando...";
            button.disabled = true;

            // alert(url)
            var form = document.getElementById(eId);
            let data = me.serializeForm(form);
            // console.log('data',data)
            // alert(form.action+'-'+form.method)
            // let formData = new FormData(form)
            // console.log("form", form);
            // console.log('DATA',formData)
            let response = await axios({
                method: form.method,
                url: form.action,
                data: data,
            });

            var arreglo = response.data.errores;
            let cadena_errors = "";
            Object.values(arreglo).forEach((val) => {
                cadena_errors += val + ", ";
            });

            if (response.data.estado == false) {
                document.getElementById(error).innerHTML =
                    '<div style = "margin-top:10px;" > <div style="background-color: #ffdbdf !important;" class="alert alert-danger text-left alert-dismissible" role="alert"><button type = "button" class="close" data-dismiss = "alert">&times;</button><strong>Corregir los Siguientes Errores:</strong> ' +
                    cadena_errors.slice(0, -2) +
                    "</a></div ></div >";
            } else {
                document.getElementById(error).innerHTML =
                    '<div style = "margin-top:10px;"><div class="alert alert-success text-left alert-dismissible" role="alert"><button type = "button" class="close" data-dismiss = "alert">&times;</button><strong>' +
                    cadena_errors.slice(0, -2) +
                    "</strong></a></div ></div>";

                if (elemento != "" && elemento != null) {
                    this.$parent.$emit("listarOrdenes");
                    window.setTimeout(function () {
                        $("#" + elemento).modal("hide");
                        $("body").removeClass("modal-open");
                        $(".modal-backdrop").remove();
                    }, 1000);
                }
            }

            button.disabled = false;
            button.innerHTML = icon + " Guardar";
        },
        async enviarFormModalAR(elemento, eId, error) {
            let me = this;
            let icon = '<i class="mdi mdi-check-bold icon-size"></i>';
            let button = document.getElementById("btnEnvio");
            button.innerHTML = icon + " Cargando...";
            button.disabled = true;

            // alert(url)
            var form = document.getElementById(eId);
            let data = me.serializeForm(form);
            // console.log('data',data)
            // alert(form.action+'-'+form.method)
            // let formData = new FormData(form)
            // console.log("form", form);
            // console.log('DATA',formData)
            let response = await axios({
                method: form.method,
                url: form.action,
                data: data,
            });

            var arreglo = response.data.errores;
            let cadena_errors = "";
            Object.values(arreglo).forEach((val) => {
                cadena_errors += val + ", ";
            });

            if (response.data.estado == false) {
                document.getElementById(error).innerHTML =
                    '<div style = "margin-top:10px;" > <div style="background-color: #ffdbdf !important;" class="alert alert-danger text-left alert-dismissible" role="alert"><button type = "button" class="close" data-dismiss = "alert">&times;</button><strong>Corregir los Siguientes Errores:</strong> ' +
                    cadena_errors.slice(0, -2) +
                    "</a></div ></div >";
            } else {
                document.getElementById(error).innerHTML =
                    '<div style = "margin-top:10px;"><div class="alert alert-success text-left alert-dismissible" role="alert"><button type = "button" class="close" data-dismiss = "alert">&times;</button><strong>' +
                    cadena_errors.slice(0, -2) +
                    "</strong></a></div ></div>";

                if (elemento != "" && elemento != null) {
                    this.$parent.$emit("buscarEvaluacion");
                    window.setTimeout(function () {
                        $("#" + elemento).modal("hide");
                        $("body").removeClass("modal-open");
                        $(".modal-backdrop").remove();
                    }, 1000);
                }
            }

            button.innerHTML = icon + " Guardar";
            button.disabled = false;
        },
        async enviarFormModalMov(elemento) {
            let me = this;
            // alert(url)
            let icon = '<i class="mdi mdi-check-bold icon-size"></i>';
            let button = document.getElementById("btnGuardar");
            button.innerHTML = icon + " Cargando...";
            button.disabled = true;

            var form = document.getElementById("formEnvModal02");
            let data = me.serializeForm(form);
            // console.log('data',data)
            // alert(form.action+'-'+form.method)
            // let formData = new FormData(form)
            // console.log("form", form);
            // console.log('DATA',formData)
            let response = await axios({
                method: form.method,
                url: form.action,
                data: data,
            });

            var arreglo = response.data.errores;
            let cadena_errors = "";
            Object.values(arreglo).forEach((val) => {
                cadena_errors += val + ", ";
            });

            if (response.data.estado == false) {
                document.getElementById("data-error-modal-mov").innerHTML =
                    '<div style = "margin-top:10px;" > <div style="background-color: #ffdbdf !important;" class="alert alert-danger text-left alert-dismissible" role="alert"><button type = "button" class="close" data-dismiss = "alert">&times;</button><strong>Corregir los Siguientes Errores:</strong> ' +
                    cadena_errors.slice(0, -2) +
                    "</a></div ></div >";
            } else {
                document.getElementById("data-error-modal-mov").innerHTML =
                    '<div style = "margin-top:10px;"><div class="alert alert-success text-left alert-dismissible" role="alert"><button type = "button" class="close" data-dismiss = "alert">&times;</button><strong>' +
                    cadena_errors.slice(0, -2) +
                    "</strong></a></div ></div>";

                if (elemento != "" && elemento != null) {
                    this.$parent.$emit("buscar");
                    window.setTimeout(function () {
                        $("#" + elemento).modal("hide");
                        $("body").removeClass("modal-open");
                        $(".modal-backdrop").remove();

                        $('#header-sec').css('z-index','')
                        $('#aside-sec').css('z-index','')      
                    }, 1000);
                }
            }
            button.disabled = false;
            button.innerHTML = icon + " Guardar";
        },
        async enviarFormModalCerrar(elemento) {
            let me = this;
            // alert(url)
            let icon = '<i class="mdi mdi-check-bold icon-size"></i>';
            let button = document.getElementById("btnGuardarC");
            button.innerHTML = icon + " Cargando...";
            button.disabled = true;

            var form = document.getElementById("formEnvModal03");
            let data = me.serializeForm(form);
            // console.log('data',data)
            // alert(form.action+'-'+form.method)
            // let formData = new FormData(form)
            // console.log("form", form);
            // console.log('DATA',formData)
            let response = await axios({
                method: form.method,
                url: form.action,
                data: data,
            });

            var arreglo = response.data.errores;
            let cadena_errors = "";
            Object.values(arreglo).forEach((val) => {
                cadena_errors += val + ", ";
            });

            if (response.data.estado == false) {
                document.getElementById("data-error-modal-cerrar").innerHTML =
                    '<div style = "margin-top:10px;" > <div style="background-color: #ffdbdf !important;" class="alert alert-danger text-left alert-dismissible" role="alert"><button type = "button" class="close" data-dismiss = "alert">&times;</button><strong>Corregir los Siguientes Errores:</strong> ' +
                    cadena_errors.slice(0, -2) +
                    "</a></div ></div >";
            } else {
                document.getElementById("data-error-modal-cerrar").innerHTML =
                    '<div style = "margin-top:10px;"><div class="alert alert-success text-left alert-dismissible" role="alert"><button type = "button" class="close" data-dismiss = "alert">&times;</button><strong>' +
                    cadena_errors.slice(0, -2) +
                    "</strong></a></div ></div>";

                if (elemento != "" && elemento != null) {
                    this.$parent.$emit("buscar");
                    window.setTimeout(function () {
                        $("#" + elemento).modal("hide");
                        $("body").removeClass("modal-open");
                        $(".modal-backdrop").remove();
                    }, 1000);
                }
            }
            button.disabled = false;
            button.innerHTML = icon + " Guardar";
        },
        async enviarFormModalCita(elemento, calendario, tipo) {
            let me = this;
            // console.log(calendario)
            var form = document.getElementById("formEnv");
            let data = me.serializeForm(form);
            // console.log('data',data)
            // alert(form.action+'-'+form.method)
            // let formData = new FormData(form)
            // console.log("form", form);
            // console.log('DATA',formData)
            let response = await axios({
                method: form.method,
                url: form.action,
                data: data,
            });

            var arreglo = response.data.errores;
            let cadena_errors = "";
            Object.values(arreglo).forEach((val) => {
                cadena_errors += val + ", ";
            });

            if (response.data.estado == false) {
                document.getElementById("data-error").innerHTML =
                    '<div style = "margin-top:10px;" > <div style="background-color: #ffdbdf !important;" class="alert alert-danger text-left alert-dismissible" role="alert"><button type = "button" class="close" data-dismiss = "alert">&times;</button><strong>Corregir los Siguientes Errores:</strong> ' +
                    cadena_errors.slice(0, -2) +
                    "</a></div ></div >";
            } else {
                document.getElementById("data-error").innerHTML =
                    '<div style = "margin-top:10px;"><div class="alert alert-success text-left alert-dismissible" role="alert"><button type = "button" class="close" data-dismiss = "alert">&times;</button><strong>' +
                    cadena_errors.slice(0, -2) +
                    "</strong></a></div ></div>";

                if (elemento != "" && elemento != null) {
                    let calendarApi;
                    if (tipo == 1) {
                        if (calendario != null) {
                            calendarApi = calendario.view.calendar;
                        } else {
                            calendarApi = calendario;
                        }
                    } else {
                        calendarApi = calendario;
                    }
                    this.$parent.$emit("loadCitasInit");

                    // let arr = await this.cargarcitas()
                    // if (calendarApi != null) {
                    //     calendarApi.removeAllEventSources()
                    //     calendarApi.addEventSource(arr)
                    //     calendarApi.refetchEvents()
                    // }
                    // calendarApi.
                    // calendarApi.unselect() // clear date selection

                    // if (title) {
                    //   calendarApi.addEvent({
                    //     id: createEventId(),
                    //     title,
                    //     start: selectInfo.startStr,
                    //     end: selectInfo.endStr,
                    //     allDay: selectInfo.allDay
                    //   })
                    // }
                    window.setTimeout(function () {
                        $("#" + elemento).modal("hide");
                        $("body").removeClass("modal-open");
                        $(".modal-backdrop").remove();
                    }, 1000);
                }
            }
        },
        async cargarcitas() {
            let response = await axios.get("/cargarcitas");

            let currentEvents = response.data.citas;
            var events = [];
            for (let i = 0; i < currentEvents.length; i++) {
                const e = currentEvents[i];
                events.push({
                    id: e.id,
                    title: e.title,
                    start: e.start,
                    end: e.end,
                    allDay: e.allDay,
                    backgroundColor: e.color,
                    textColor: e.textColor,
                });
            }
            return events;
        },
        async enviarFormModalAnulacion(elemento) {
            let me = this;
            // alert(url)
            let icon = '<i class="mdi mdi-check-bold icon-size"></i>';
            let button = document.getElementById("btnGuardar");
            button.innerHTML = icon + " Cargando...";
            button.disabled = true;

            var form = document.getElementById("formEnvModalAnulacion");
            let data = me.serializeForm(form);
            // console.log('data',data)
            // alert(form.action+'-'+form.method)
            // let formData = new FormData(form)
            // console.log("form", form);
            // console.log('DATA',formData)
            let response = await axios({
                method: form.method,
                url: form.action,
                data: data,
            });

            var arreglo = response.data.errores;
            let cadena_errors = "";
            Object.values(arreglo).forEach((val) => {
                cadena_errors += val + ", ";
            });

            if (response.data.estado == false) {
                document.getElementById(
                    "data-error-modal-anulacion"
                ).innerHTML =
                    '<div style = "margin-top:10px;" > <div style="background-color: #ffdbdf !important;" class="alert alert-danger text-left alert-dismissible" role="alert"><button type = "button" class="close" data-dismiss = "alert">&times;</button><strong>Corregir los Siguientes Errores:</strong> ' +
                    cadena_errors.slice(0, -2) +
                    "</a></div ></div >";
            } else {
                document.getElementById(
                    "data-error-modal-anulacion"
                ).innerHTML =
                    '<div style = "margin-top:10px;"><div class="alert alert-success text-left alert-dismissible" role="alert"><button type = "button" class="close" data-dismiss = "alert">&times;</button><strong>' +
                    cadena_errors.slice(0, -2) +
                    "</strong></a></div ></div>";

                if (elemento != "" && elemento != null) {
                    this.$parent.$emit("buscarComprobantes");
                    window.setTimeout(function () {
                        $("#" + elemento).modal("hide");
                        $("body").removeClass("modal-open");
                        $(".modal-backdrop").remove();
                    }, 1000);
                }
            }

            button.innerHTML = icon + " Guardar";
            button.disabled = false;
        },
        isEncontrado(arreglo, valor) {
            return arreglo.includes(valor);
        },
        generarAleatorio(min, max, arreglo) {
            let band = false;
            let num = 0;
            do {
                num = Math.floor(Math.random() * (max + 1) - min) + min;
                band = this.isEncontrado(arreglo, num);
            } while (band == true);

            return num;
        },
        async enviarFormModalAct(
            elemento,
            entidad,
            tipoListado,
            movValid = null,
            calendario = null,
            tipoEnvio = null
        ) {
            let me = this;
            let icon = '<i class="mdi mdi-check-bold icon-size"></i>';
            let button = document.getElementById("btnEnvio_" + entidad);
            button.innerHTML = icon + " Cargando...";
            button.disabled = true;

            let bandera = false;
            if (movValid != null) {
                if (movValid == "MOV_A") {
                    let tipoMov = $(`select_tipo_operacion_${entidad}`).value;
                    if (tipoMov == "E") {
                        bandera = me.validarDetallesCompras();
                    } else {
                        bandera = me.validarDetallesComprasInv(entidad);
                    }
                } else if (movValid == "MOV_AT") {
                    let tipoMov = $(`select_tipo_operacion_${entidad}`).value;
                    if (tipoMov == "E") {
                        bandera = me.validarDetalleCompraAuto();
                    } else {
                        bandera = me.validarDetalleCotizacion(entidad);
                    }
                }
            } else {
                bandera = true;
            }

            if (bandera) {
                var form = document.getElementById(`formEnvModal_${entidad}`);
                let data = me.serializeForm(form);

                let response = await axios({
                    method: form.method,
                    url: form.action,
                    data: data,
                });

                var arreglo = response.data.errores;
                let cadena_errors = "";
                Object.values(arreglo).forEach((val) => {
                    cadena_errors += val + ", ";
                });

                let calendarApi;
                if (tipoEnvio != null) {
                    if (tipoEnvio == 1) {
                        if (calendario != null) {
                            calendarApi = calendario.view.calendar;
                        } else {
                            calendarApi = calendario;
                        }
                    } else {
                        calendarApi = calendario;
                    }
                }

                if (response.data.estado == false) {
                    document.getElementById(
                        `data-error-modal_${entidad}`
                    ).innerHTML =
                        '<div style = "margin-top:10px;" > <div style="background-color: #ffdbdf !important;" class="alert alert-danger text-left alert-dismissible" role="alert"><button type = "button" class="close" data-dismiss = "alert">&times;</button><strong>Corregir los Siguientes Errores:</strong> ' +
                        cadena_errors.slice(0, -2) +
                        "</a></div ></div >";
                } else {
                    document.getElementById(
                        `data-error-modal_${entidad}`
                    ).innerHTML =
                        '<div style = "margin-top:10px;"><div class="alert alert-success text-left alert-dismissible" role="alert"><button type = "button" class="close" data-dismiss = "alert">&times;</button><strong>' +
                        cadena_errors.slice(0, -2) +
                        "</strong></a></div ></div>";

                    if (elemento != "" && elemento != null) {
                        if (tipoListado == "CLI") {
                            this.$parent.$emit("buscarCliente");
                        } else if (tipoListado == "CIT") {
                            this.$parent.$emit("loadCitasInit");

                            // let arr = await this.cargarcitas()
                            // if (calendarApi != null) {
                            //     calendarApi.removeAllEventSources()
                            //     calendarApi.addEventSource(arr)
                            //     calendarApi.refetchEvents()
                            // }
                        }

                        window.setTimeout(function () {
                            $("#" + elemento).modal("hide");
                            document.getElementById(
                                "data-error-modal_" + entidad
                            ).innerHTML = "";
                            $("body").removeClass("modal-open");
                            $(".modal-backdrop").remove();
                        }, 1000);
                    }
                }
            }

            button.innerHTML = icon + " Guardar";
            button.disabled = false;
        },
        async enviarFormModalActCita(
            elemento,
            entidad,
            tipoListado,
            calendario = null,
            tipoEnvio = null
        ) {
            let me = this;
            let icon = '<i class="mdi mdi-check-bold icon-size"></i>';
            let button = document.getElementById("btnEnvio_" + entidad);
            button.innerHTML = icon + " Cargando...";
            button.disabled = true;

            let bandera = true;
            
            if (bandera) {
                var form = document.getElementById(`formEnvModal_${entidad}`);
                let data = me.serializeForm(form);

                let response = await axios({
                    method: form.method,
                    url: form.action,
                    data: data,
                });

                var arreglo = response.data.errores;
                let cadena_errors = "";
                Object.values(arreglo).forEach((val) => {
                    cadena_errors += val + ", ";
                });

                let calendarApi;
                if (tipoEnvio != null) {
                    if (tipoEnvio == 1) {
                        if (calendario != null) {
                            calendarApi = calendario.view.calendar;
                        } else {
                            calendarApi = calendario;
                        }
                    } else {
                        calendarApi = calendario;
                    }
                }

                if (response.data.estado == false) {
                    document.getElementById(
                        `data-error-modal_${entidad}`
                    ).innerHTML =
                        '<div style = "margin-top:10px;" > <div style="background-color: #ffdbdf !important;" class="alert alert-danger text-left alert-dismissible" role="alert"><button type = "button" class="close" data-dismiss = "alert">&times;</button><strong>Corregir los Siguientes Errores:</strong> ' +
                        cadena_errors.slice(0, -2) +
                        "</a></div ></div >";
                } else {
                    document.getElementById(
                        `data-error-modal_${entidad}`
                    ).innerHTML =
                        '<div style = "margin-top:10px;"><div class="alert alert-success text-left alert-dismissible" role="alert"><button type = "button" class="close" data-dismiss = "alert">&times;</button><strong>' +
                        cadena_errors.slice(0, -2) +
                        "</strong></a></div ></div>";

                    if (elemento != "" && elemento != null) {
                        if (tipoListado == "CLI") {
                            this.$parent.$emit("buscarCliente");
                        } else if (tipoListado == "CIT") {
                            this.$parent.$emit("loadCitasInit");

                            // let arr = await this.cargarcitas()
                            // if (calendarApi != null) {
                            //     calendarApi.removeAllEventSources()
                            //     calendarApi.addEventSource(arr)
                            //     calendarApi.refetchEvents()
                            // }
                        }

                        window.setTimeout(function () {
                            $("#" + elemento).modal("hide");
                            document.getElementById(
                                "data-error-modal_" + entidad
                            ).innerHTML = "";
                            $("body").removeClass("modal-open");
                            $(".modal-backdrop").remove();
                        }, 1000);
                    }
                }
            }

            button.innerHTML = icon + " Guardar";
            button.disabled = false;
        },
        async enviarFormModalActCliente(
            elemento,
            entidad,
            tipoListado,
            tipoEnv,
            movValid = null,
            calendario = null,
            tipoEnvio = null
        ) {
            let me = this;
            let icon = '<i class="mdi mdi-check-bold icon-size"></i>';
            let button = document.getElementById("btnEnvio_" + entidad);
            button.innerHTML = icon + " Cargando...";
            button.disabled = true;

            let bandera = false;
            if (movValid != null) {
                if (movValid == "MOV_A") {
                    let tipoMov = $(`select_tipo_operacion_${entidad}`).value;
                    if (tipoMov == "E") {
                        bandera = me.validarDetallesCompras();
                    } else {
                        bandera = me.validarDetallesComprasInv(entidad);
                    }
                } else if (movValid == "MOV_AT") {
                    let tipoMov = $(`select_tipo_operacion_${entidad}`).value;
                    if (tipoMov == "E") {
                        bandera = me.validarDetalleCompraAuto();
                    } else {
                        bandera = me.validarDetalleCotizacion(entidad);
                    }
                }
            } else {
                bandera = true;
            }

            if (bandera) {
                var form = document.getElementById(`formEnvModal_${entidad}`);
                let data = me.serializeForm(form);

                let response = await axios({
                    method: form.method,
                    url: form.action,
                    data: data,
                });

                var arreglo = response.data.errores;
                let cadena_errors = "";
                Object.values(arreglo).forEach((val) => {
                    cadena_errors += val + ", ";
                });

                let calendarApi;
                if (tipoEnvio != null) {
                    if (tipoEnvio == 1) {
                        if (calendario != null) {
                            calendarApi = calendario.view.calendar;
                        } else {
                            calendarApi = calendario;
                        }
                    } else {
                        calendarApi = calendario;
                    }
                }

                if (response.data.estado == false) {
                    document.getElementById(
                        `data-error-modal_${entidad}`
                    ).innerHTML =
                        '<div style = "margin-top:10px;" > <div style="background-color: #ffdbdf !important;" class="alert alert-danger text-left alert-dismissible" role="alert"><button type = "button" class="close" data-dismiss = "alert">&times;</button><strong>Corregir los Siguientes Errores:</strong> ' +
                        cadena_errors.slice(0, -2) +
                        "</a></div ></div >";
                } else {
                    document.getElementById(
                        `data-error-modal_${entidad}`
                    ).innerHTML =
                        '<div style = "margin-top:10px;"><div class="alert alert-success text-left alert-dismissible" role="alert"><button type = "button" class="close" data-dismiss = "alert">&times;</button><strong>' +
                        cadena_errors.slice(0, -2) +
                        "</strong></a></div ></div>";

                    if (elemento != "" && elemento != null) {
                        if (tipoListado == "CLI") {
                            if (tipoEnv != 3) {
                                this.$parent.$emit("buscarCliente");
                            } else {
                                this.$parent.$emit("busquedaListCliente");
                            }
                        } else if (tipoListado == "CIT") {
                            this.$parent.$emit("loadCitasInit");

                            // let arr = await this.cargarcitas()
                            // if (calendarApi != null) {
                            //     calendarApi.removeAllEventSources()
                            //     calendarApi.addEventSource(arr)
                            //     calendarApi.refetchEvents()
                            // }
                        }

                        window.setTimeout(function () {
                            $('#'+elemento).modal('toggle')
                            $('#'+elemento).css('z-index', '-1')
                            $('#header-sec').css('z-index','')
                            $('#aside-sec').css('z-index','')
                        }, 1000);
                    }
                }
            }

            button.innerHTML = icon + " Guardar";
            button.disabled = false;
        },

        async enviarFormModalActProveedor(
            elemento,
            entidad,
            tipoListado,
            tipoEnv,
            movValid = null,
            calendario = null,
            tipoEnvio = null
        ) {
            let me = this;
            let icon = '<i class="mdi mdi-check-bold icon-size"></i>';
            let button = document.getElementById("btnEnvio_" + entidad);
            button.innerHTML = icon + " Cargando...";
            button.disabled = true;

            let bandera = false;
            if (movValid != null) {
                if (movValid == "MOV_A") {
                    let tipoMov = $(`select_tipo_operacion_${entidad}`).value;
                    if (tipoMov == "E") {
                        bandera = me.validarDetallesCompras();
                    } else {
                        bandera = me.validarDetallesComprasInv(entidad);
                    }
                } else if (movValid == "MOV_AT") {
                    let tipoMov = $(`select_tipo_operacion_${entidad}`).value;
                    if (tipoMov == "E") {
                        bandera = me.validarDetalleCompraAuto();
                    } else {
                        bandera = me.validarDetalleCotizacion(entidad);
                    }
                }
            } else {
                bandera = true;
            }

            if (bandera) {
                var form = document.getElementById(`formEnvModal_${entidad}`);
                let data = me.serializeForm(form);

                let response = await axios({
                    method: form.method,
                    url: form.action,
                    data: data,
                });

                var arreglo = response.data.errores;
                let cadena_errors = "";
                Object.values(arreglo).forEach((val) => {
                    cadena_errors += val + ", ";
                });

                let calendarApi;
                if (tipoEnvio != null) {
                    if (tipoEnvio == 1) {
                        if (calendario != null) {
                            calendarApi = calendario.view.calendar;
                        } else {
                            calendarApi = calendario;
                        }
                    } else {
                        calendarApi = calendario;
                    }
                }

                if (response.data.estado == false) {
                    document.getElementById(
                        `data-error-modal_${entidad}`
                    ).innerHTML =
                        '<div style = "margin-top:10px;" > <div style="background-color: #ffdbdf !important;" class="alert alert-danger text-left alert-dismissible" role="alert"><button type = "button" class="close" data-dismiss = "alert">&times;</button><strong>Corregir los Siguientes Errores:</strong> ' +
                        cadena_errors.slice(0, -2) +
                        "</a></div ></div >";
                } else {
                    document.getElementById(
                        `data-error-modal_${entidad}`
                    ).innerHTML =
                        '<div style = "margin-top:10px;"><div class="alert alert-success text-left alert-dismissible" role="alert"><button type = "button" class="close" data-dismiss = "alert">&times;</button><strong>' +
                        cadena_errors.slice(0, -2) +
                        "</strong></a></div ></div>";

                    if (elemento != "" && elemento != null) {
                        if (tipoListado == "PROV") {
                            if (tipoEnv != 3) {
                                this.$parent.$emit("buscarProveedor");
                            } else {
                                this.$parent.$emit("busquedaListProveedor");
                            }
                        } else if (tipoListado == "CIT") {
                            this.$parent.$emit("loadCitasInit");

                            // let arr = await this.cargarcitas()
                            // if (calendarApi != null) {
                            //     calendarApi.removeAllEventSources()
                            //     calendarApi.addEventSource(arr)
                            //     calendarApi.refetchEvents()
                            // }
                        }

                        window.setTimeout(function () {
                            $('#'+elemento).modal('toggle')
                            $('#'+elemento).css('z-index', '-1')
                            $('#header-sec').css('z-index','')
                            $('#aside-sec').css('z-index','')
                        }, 1000);
                    }
                }
            }

            button.innerHTML = icon + " Guardar";
            button.disabled = false;
        },

        async isValidSession() {
            let response = await axios.get("/validarsession");

            if (!response.data.estado) {
                // alert("Sesión Terminada, Redireccionando...");
                // window.setTimeout(function () {
                localStorage.clear();
                this.$router.replace("/");

                // window.location.href = "/";
                // this.$router.push('/')
                // }, 150);
            } else {
                this.actualizarTipoCambio();
            }
        },
        async isIgv() {
            let response = await axios.get("/getconfigigv");
            if (response.data.estado) {
                return response.data.mostrar;
            } else {
                this.isValidSession();
            }
        },
        async actualizarTipoCambio() {
            let response = await axios.get(`/gettipocambio`);
            if (!response.data.estado) {
                let fecha = this.obtenerFechaActual();
                let res = await this.consultarTipoCambio(fecha);
                let cl = res.data;
                console.log("resp", cl.data);
                if (cl.success) {
                    let res = await axios({
                        url: "/guardartipocambioapi",
                        method: "POST",
                        data: {
                            precio_compra: cl.data.compra,
                            precio_venta: cl.data.venta,
                            fecha_sunat: cl.data.fecha_sunat,
                        },
                    });

                    if (res.data.estado) {
                        console.log("Tipo de Cambio Actualizado correctamente");
                    }
                }
            }
        },
        async enviarFormModalAperturar(elemento) {
            let me = this;
            // alert(url)
            let icon = '<i class="mdi mdi-check-bold icon-size"></i>';
            let button = document.getElementById("btnEnvioG");
            button.innerHTML = icon + " Cargando...";
            button.disabled = true;

            var form = document.getElementById("formEnvModalAperturar");
            let data = me.serializeForm(form);
            // console.log('data',data)
            // alert(form.action+'-'+form.method)
            // let formData = new FormData(form)
            // console.log("form", form);
            // console.log('DATA',formData)
            let response = await axios({
                method: form.method,
                url: form.action,
                data: data,
            });

            var arreglo = response.data.errores;
            let cadena_errors = "";
            Object.values(arreglo).forEach((val) => {
                cadena_errors += val + ", ";
            });

            if (response.data.estado == false) {
                document.getElementById(
                    "data-error-modal-aperturar"
                ).innerHTML =
                    '<div style = "margin-top:10px;" > <div style="background-color: #ffdbdf !important;" class="alert alert-danger text-left alert-dismissible" role="alert"><button type = "button" class="close" data-dismiss = "alert">&times;</button><strong>Corregir los Siguientes Errores:</strong> ' +
                    cadena_errors.slice(0, -2) +
                    "</a></div ></div >";
            } else {
                document.getElementById(
                    "data-error-modal-aperturar"
                ).innerHTML =
                    '<div style = "margin-top:10px;"><div class="alert alert-success text-left alert-dismissible" role="alert"><button type = "button" class="close" data-dismiss = "alert">&times;</button><strong>' +
                    cadena_errors.slice(0, -2) +
                    "</strong></a></div ></div>";

                if (elemento != "" && elemento != null) {
                    this.$parent.$emit("buscar");
                    window.setTimeout(function () {
                        $("#" + elemento).modal("hide");
                        $("body").removeClass("modal-open");
                        $(".modal-backdrop").remove();
                    }, 1000);
                }
            }

            button.innerHTML = icon + " Guardar";
            button.disabled = false;
        },
        importarScript(nombre, refId) {
            var s = document.createElement("script");
            s.setAttribute("src", nombre);
            s.setAttribute("id", refId);
            // s.async = true;
            document.querySelector("body").appendChild(s);
        },
        deleteScript (refId) {
            var script = document.getElementById(`${refId}`);
            if (script) script.remove();
        },
        initAnimation() {
            let srcElem = document.getElementById("script-reload");
            srcElem.src = "/js/assets/core/app.min.js";
            console.log("Importado...");
        },
        reloadFirstLoad() {
            let permitido = localStorage.getItem("permitido");
            if (permitido == "true") {
                if (screen.width < 1023) {
                    // console.log('Dalee...');
                    // window.location.reload()
                    // console.log("Primera Carga...")
                }
            }
            localStorage.removeItem("permitido");
        },
        async consultarApi(dato, tipo) {
            // let dato = e.value
            let url = "";
            // let resultado = null
            if (tipo == 1) {
                url = "https://apiperu.dev/api/dni/" + dato;
            } else {
                url = "https://apiperu.dev/api/ruc/" + dato;
            }

            let resultado = axios({
                url: url,
                headers: {
                    "Content-Type": "application/json",
                    Authorization:
                        "Bearer b842bef3b15a5e417c93f99694cfa68b5a312decbce6cbea6d089b25b4dab446",
                },
                method: "GET",
                dataType: "json",
            })
                .then(function (result) {
                    return result;
                })
                .catch(function (error) {
                    return error;
                });

            return resultado;
        },
        roundToTwo(num) {
            return +(Math.round(num + "e+2")  + "e-2");
        },
        async consultarTipoCambio(dato) {
            let resultado = axios({
                url: "https://apiperu.dev/api/tipo_de_cambio",
                headers: {
                    "Content-Type": "application/json",
                    Authorization:
                        "Bearer b842bef3b15a5e417c93f99694cfa68b5a312decbce6cbea6d089b25b4dab446",
                },
                data: {
                    fecha: dato,
                },
                method: "POST",
                dataType: "json",
            })
                .then(function (result) {
                    return result;
                })
                .catch(function (error) {
                    return error;
                });

            return resultado;
        },
        bloquearButton(idButton) {
            let button = document.getElementById(`${idButton}`);
            button.disabled = true;
            button.innerHTML = `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Cargando...`;
        },
        desbloquearButton(idButton, htmlInsert) {
            let button = document.getElementById(`${idButton}`);
            button.disabled = false;
            button.innerHTML = `${htmlInsert}`;
        },
        async loadNotifications() {
            setInterval(async function () {
                let response = await axios.get('/notificaciones/all');
                let data = response.data.notificaciones;
                let data2 = response.data.mensajes;
                let me = this;
                document.getElementById('numNotificaciones').innerHTML = `${data.length==0?'No cuenta con':data.length +' Nuevas'}`;
                let datosRender = ``;
                data.forEach(el => {
                    datosRender += `<a href="javascript:void(0)" class="message-item">
                        <span class="btn btn-${el.color} btn-circle">
                            <i class="${el.icono}"></i>
                        </span>
                        <div class="mail-contnet">
                            <h5 class="message-title">${el.titulo}</h5>
                            <span class="mail-desc" data-toggle="tooltip" data-placement="bottom" title="${el.mensaje}">${el.mensaje}</span> 
                            <span class="time">${el.hora}</span> 
                        </div>
                    </a>`;
                });
                document.getElementById('renderNotificaciones').innerHTML = datosRender;

                document.getElementById('numMensajes').innerHTML = `${data2.length==0?'No cuenta con':data2.length +' Nuevas'}`;
                let datosRender2 = ``;
                let MAX_LENGTH_MESSAGE = 21;
                data2.forEach(el => {
                    datosRender2 += `<a href="javascript:void(0)" class="message-item">
                        <span class="btn btn-${el.color} btn-circle">
                            <i class="${el.icono}"></i>
                        </span>
                        <div class="mail-contnet">
                            <h5 class="message-title">${el.titulo}</h5>
                            <span class="mail-desc" data-toggle="tooltip" data-placement="bottom"  
                                title="${el.mensaje}">${el.mensaje.length>MAX_LENGTH_MESSAGE?el.mensaje.substring(0, MAX_LENGTH_MESSAGE)+'...':el.mensaje}</span>
                            <span class="time">${el.hora}</span> 
                        </div>
                    </a>`;
                });
                document.getElementById('renderMensajes').innerHTML = datosRender2;
                $('[data-toggle="tooltip"]').tooltip();
            }, 30000);
        },
        async loadFirstNotifications () {
            let response = await axios.get('/notificaciones/all');
            let data = response.data.notificaciones;
            let data2 = response.data.mensajes;
            let me = this;
            document.getElementById('numNotificaciones').innerHTML = `${data.length==0?'No cuenta con':data.length +' Nuevas'}`;
            let datosRender = ``;
            data.forEach(el => {
                datosRender += `<a href="javascript:void(0)" class="message-item">
                    <span class="btn btn-${el.color} btn-circle">
                        <i class="${el.icono}"></i>
                    </span>
                    <div class="mail-contnet">
                        <h5 class="message-title">${el.titulo}</h5>
                        <span class="mail-desc" data-toggle="tooltip" data-placement="bottom" title="${el.mensaje}">${el.mensaje}</span> 
                        <span class="time">${el.hora}</span> 
                    </div>
                </a>`;
            });
            document.getElementById('renderNotificaciones').innerHTML = datosRender;

            document.getElementById('numMensajes').innerHTML = `${data2.length==0?'No cuenta con':data2.length +' Nuevas'}`;
            let datosRender2 = ``;
            let MAX_LENGTH_MESSAGE = 21;
            data2.forEach(el => {    
                datosRender2 += `<a href="javascript:void(0)" class="message-item">
                    <span class="btn btn-${el.color} btn-circle">
                        <i class="${el.icono}"></i>
                    </span>
                    <div class="mail-contnet">
                        <h5 class="message-title">${el.titulo}</h5>
                        <span class="mail-desc" data-toggle="tooltip" data-placement="bottom" 
                            title="${el.mensaje}">${el.mensaje.length>MAX_LENGTH_MESSAGE?el.mensaje.substring(0, MAX_LENGTH_MESSAGE)+'...':el.mensaje}</span> 
                        <span class="time">${el.hora}</span> 
                    </div>
                </a>`;
            });
            document.getElementById('renderMensajes').innerHTML = datosRender2;
            me.inicializarTooltips();
            me.loadNotifications();
        }
    },
    beforeUpdate() {},
    updated() {
        // this.obtenerFechaActual()
    },
    mounted() {
        // window.axios.defaults.headers.common['X-CSRF-TOKEN'] = document.getElementById('tokenApp').value
        // eslint-disable-next-line no-undef
        // $('#tabla').on('click', 'tbody tr', function () {
        //     // alert('click')
        //     // eslint-disable-next-line no-undef
        //     if ($(this).hasClass('selected')) {
        //         // eslint-disable-next-line no-undef
        //         // $(this).removeClass('selected')
        //     } else {
        //         // eslint-disable-next-line no-undef
        //         $(this).addClass('selected')
        //     }
        // })
        // // eslint-disable-next-line no-undef
        // $('#tabla02').on('click', 'tbody tr', function () {
        //     // alert('click')
        //     // eslint-disable-next-line no-undef
        //     if ($(this).hasClass('selected')) {
        //         // eslint-disable-next-line no-undef
        //         // $(this).removeClass('selected')
        //     } else {
        //         // eslint-disable-next-line no-undef
        //         $(this).addClass('selected')
        //     }
        // })
        // // eslint-disable-next-line no-undef
        // $('#tabla03').on('click', 'tbody tr', function () {
        //     // alert('click')
        //     // eslint-disable-next-line no-undef
        //     if ($(this).hasClass('selected')) {
        //         // eslint-disable-next-line no-undef
        //         // $(this).removeClass('selected')
        //     } else {
        //         // eslint-disable-next-line no-undef
        //         $(this).addClass('selected')
        //     }
        // })
        // eslint-disable-next-line no-undef
        // window.onbeforeunload = function (e) {
        //     return '¿Cancelar todo?'
        // }
        // $(window).on('beforeunload', function (e) {
        //     return '¿Cancelar todo?'
        // })
        // $('button:submit').click(function () { return false })
    },
    destroyed() {
        // console.log('Que Fueee')
    },
};

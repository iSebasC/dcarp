function soloNumeros(e) {
    let key = window.Event ? e.which : e.keyCode
    let tecla = String.fromCharCode(key).toLowerCase()
    // alert(tecla)
    let caracteresValidos = '0123456789'
    // eslint-disable-next-line camelcase
    if (caracteresValidos.indexOf(tecla) === -1) {
        e.preventDefault()
        return false
    }

    return true
}

function soloLetras(e) {
    let key = e.keyCode || e.which
    let tecla = String.fromCharCode(key).toLowerCase()
    let letras = ' áéíóúabcdefghijklmnñopqrstuvwxyz'
    let especiales = [8, 37, 39, 46]

    // eslint-disable-next-line camelcase
    let tecla_especial = false
    for (var i in especiales) {
        if (key === especiales[i]) {
            // eslint-disable-next-line camelcase
            tecla_especial = true
            break
        }
    }

    // eslint-disable-next-line camelcase
    if (letras.indexOf(tecla) === -1 && !tecla_especial) {
        e.preventDefault()
        // return false
    }
}

function soloNumerosGuion (e) {
    let key = window.Event ? e.which : e.keyCode
    let tecla = String.fromCharCode(key).toLowerCase()
    // alert(tecla)
    let caracteresValidos = 'abcdefghijklmnñopqrstuvwxyz0123456789-'
    // eslint-disable-next-line camelcase
    if (caracteresValidos.indexOf(tecla) === -1) {
        e.preventDefault()
        return false
    }

    return true
}

function tipoCliente (el) {
    let valor = el.value
    let razonSocial = document.getElementById('razon_social_persona')
    let apellidos   = document.getElementById('apellidos_persona')
    let nombres     = document.getElementById('nombres_persona')
    let documento   = document.getElementById('documento')
    let max_caracteres = 8
    
    razonSocial.classList.toggle('ocultar')
    apellidos.classList.toggle('ocultar')
    nombres.classList.toggle('ocultar')
    
    if (valor == 'F') {
        max_caracteres = 11
    }

    documento.setAttribute('minlength',max_caracteres)
    documento.setAttribute('maxlength',max_caracteres)
    documento.focus()
}

async function consultarApi (dato, tipo) {
    // let dato = e.value
    let  url = ''
    // let resultado = null
    if (tipo == 1) {
        url = 'https://apiperu.dev/api/dni/'+dato
    } else {
        url = 'https://apiperu.dev/api/ruc/'+dato
    }

    let resultado = axios({
        url: url,
        headers: {
            "Content-Type": "application/json",
            "Authorization": "Bearer b842bef3b15a5e417c93f99694cfa68b5a312decbce6cbea6d089b25b4dab446"
        },
        method:'GET',
        dataType: 'json'
    }).then(function (result) {
        return result
    }).catch(function (error) {
        return error
    })

    return resultado
}

async function getMarcasAuto () {
    let response = await axios.get('/marcasauto')
    let marcas = response.data.marcasauto
    let html = '<option value="" selected="" disabled="">Seleccione</option>'
    marcas.forEach (el => {
        html += `<option value="${el.id}">${el.nombre}</option>`
    })

    html += '<option value="otro">Indique otra</option>'

    document.getElementById('select_marca').innerHTML = html
    document.getElementById('data-cliente').innerHTML = ''
}

async function buscarCliente (e) {
    let documento = document.getElementById('documento').value
    let cliente = null
    if (e.keyCode === 13) {
        let val = document.getElementById('select_tipodocumento').value
        document.getElementById('data-cliente').innerHTML = ''
        if (val == '') {
            document.getElementById('data-cliente').innerHTML = '<div style = "margin-top:10px;"><div class="alert alert-danger text-left alert-dismissible" role="alert"><strong>Seleccione Tipo de Documento Antes de Seguir...</strong></a></div ></div>'
        } else {
            if (documento.length == 8 || documento.length == 11) { 
                document.getElementById('data-cliente').innerHTML = '<div style = "margin-top:10px;"><div class="alert alert-warning text-left alert-dismissible" role="alert"><strong>Espere...</strong></a></div ></div>'
                let response = await axios({
                    method: 'post',
                    url: '/getcliente/'+documento
                })
                
            
                if (!response.data.estado) {
                    let res = await consultarApi(documento,(documento.length==8?1:2))
                    let cl  = res.data  
                    // console.log('resp', cl);
                    if (cl.success) {
                        let client = cl.data
                        // console.log(cl);
                        if (documento.length==11) {
                            cliente = {
                                'razonSocial': client.nombre_o_razon_social,
                                'direccion'  : client.direccion_completa
                            }
                        } else {
                            cliente = {
                                'apellidos': client.apellido_paterno+' '+client.apellido_materno,
                                'nombres'  : client.nombres
                            }
                        }
                    }
                } else {
                    cliente = null
                }

                let bandValidacion = true
                // window.setTimeout( function () {
                document.getElementById('data-cliente').innerHTML = ''
                if (response.data.estado) {
                    cliente = response.data.cliente
                    if (cliente.apellidos == null && cliente.nombres == null && cliente.razonSocial == null) {
                        bandValidacion = false
                    }

                    if (!bandValidacion) {
                        document.getElementById('data-cliente').innerHTML = '<div style = "margin-top:10px;"><div class="alert alert-warning text-left alert-dismissible" role="alert"><strong>Documento no encontrado, Registre por favor...</strong></a></div ></div>'
                    }
                } else {
                    // cliente = null
                    // console.log('cliente', cliente);
                    if (cliente == null) {
                        document.getElementById('data-cliente').innerHTML = '<div style = "margin-top:10px;"><div class="alert alert-warning text-left alert-dismissible" role="alert"><strong>Documento no encontrado, Registre por favor...</strong></a></div ></div>'
                    }
                }


                if (cliente.razonSocial != null) {
                    document.getElementById('razonSocial').value = cliente.razonSocial
                } else {
                    document.getElementById('apellidos').value = cliente.apellidos
                    document.getElementById('nombres').value   = cliente.nombres
                }
            } else {
                document.getElementById('data-cliente').innerHTML = '<div style = "margin-top:10px;"><div class="alert alert-warning text-left alert-dismissible" role="alert"><strong>El Documento debe ser DNI o RUC</strong></a></div ></div>'
            } 
        }
        // alert('Fijo...')
    }
}

function obtenerFechaActual() {
    // this.$store.state.fechaActual = 
    var f = new Date()
    return f.getFullYear() + '-' + ((f.getMonth() + 1) < 10 ? '0' + (f.getMonth() + 1) : (f.getMonth() + 1)) + '-' + (f.getDate() < 10 ? '0' + f.getDate() : f.getDate())
}

function obtenerHoraActual () {
    var f = new Date()
    return (f.getHours()<10?'0'+f.getHours():f.getHours())+':'+ (f.getMinutes()<10?'0'+f.getMinutes():f.getMinutes())
}

function marcaNueva (el) {
    let valor = el.value
    let marca_nueva = document.getElementById('marca_ocultar')

    if (valor == 'otro') {
        marca_nueva.classList.remove('ocultar')
    } else{
        marca_nueva.classList.add('ocultar')
    }
    document.getElementById('marca').focus()
}

function serializeForm(form) {
    var field, s = {};
    if (typeof form == 'object' && form.nodeName == "FORM") {
        var len = form.elements.length;
        for (let i=0; i<len; i++) {
            field = form.elements[i];
            if (field.name && field.type != 'file' && field.type != 'reset' && field.type != 'submit' && field.type != 'button') {
                if (field.type == 'select-multiple') {
                    for (j=form.elements[i].options.length-1; j>=0; j--) {
                        if(field.options[j].selected)
                            s[field.name] = field.options[j].value
                    }
                } else if ((field.type != 'checkbox' && field.type != 'radio') || field.checked) {
                    s[field.name] = field.value;
                } else if ((field.type == 'checkbox' || field.type == 'radio') && field.checked) {
                    s[field.name] = field.value;
                }
            }
        }
    }
    return s //JSON.stringify(s) //s.join('&').replace(/%20/g, '+');
}

async function enviarForm () {
    let button = document.getElementById('btnEnvio')
    button.innerHTML = 'Cargando...'
    button.disabled  = true

    var form = document.getElementById('formEnv')
    let data = serializeForm(form)

    // console.log('Data', data)
    // console.log('button', button)
    // let url  = 'http://carpio.ayluby.com/api/guardarcita'

    let response = await axios({
        method: form.method,
        url: form.action,
        data: data
    })

    var arreglo = response.data.errores
    let cadena_errors = '';
    Object.values(arreglo).forEach(val => {
        cadena_errors += val + ', '
    })

    if (response.data.estado == false) {
        document.getElementById('data-error').innerHTML = '<div style = "margin-top:10px;" > <div style="background-color: #ffdbdf !important;" class="alert alert-danger text-left alert-dismissible" role="alert"><strong>Corregir los Siguientes Errores:</strong> ' + cadena_errors.slice(0, -2) + '</a></div></div>'
    } else {
        document.getElementById('data-error').innerHTML = '<div style = "margin-top:10px;"><div class="alert alert-success text-left alert-dismissible" role="alert"><strong>' + cadena_errors.slice(0, -1) + '</strong></a></div ></div>'
        
        setTimeout(function () {
            window.location.href='/exito'
        },1000)
        // if (url2 != null) {
            // window.setTimeout( function () {
            //     atras(url2)
            // }, 250)
        // } else {
        //     // window.location.reload()
        // }
    }
    button.disabled = false
    button.innerHTML = 'Registrar'
}

// INIT FUNCTIONS
let iFecha = document.getElementById('fecha')
let iHora  = document.getElementById('hora')

iFecha.value = obtenerFechaActual()
iHora.value  = obtenerHoraActual()
getMarcasAuto()
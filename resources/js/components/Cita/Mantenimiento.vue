<template>
  <div
    class="modal fade"
    id="modalCita"
    tabindex="-1"
    role="dialog"
    aria-labelledby="modalCitaLabel"
    aria-hidden="true"
  >
    <div class="modal-dialog modal-xl" role="document">
      <div class="modal-content">
        <form
          class="form"
          :id="'formEnvModal_' + entidad"
          method="POST"
          action="/guardarcita"
        >
          <div class="modal-header">
            <h5 class="modal-title" id="modalCitaLabel">
              <strong v-text="tipo + ' Cita - ' + numero"></strong>
            </h5>
            <button
              type="button"
              class="close"
              @click="cerrarModal"
              aria-label="Close"
            >
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <input type="hidden" :value="numero" name="numero" />
            <input type="hidden" :value="id" name="id" />
            <input
              type="hidden"
              :value="this.$store.state.token"
              name="_token"
            />
            <h6 class="text-muted title ml-2">
              <strong>DATOS DEL CLIENTE</strong>
            </h6>
            <div class="row my-2 mx-1">
              <div class="col-md-2 col-xs-12">
                <label :for="'select_tipodocumento_' + entidad"
                  >Tipo de Persona <span class="text-danger">*</span></label
                >
                <select
                  class="custom-select custom-select-sm"
                  name="tipodocumento"
                  :id="'select_tipodocumento_' + entidad"
                  @change="tipoVenta"
                >
                  <option
                    v-for="f in tipos"
                    :key="f.id"
                    :value="f.id"
                    :selected="f.id == tipoId"
                  >
                    {{ f.nombre }}
                  </option>
                </select>
              </div>

              <div class="col-md-2 col-xs-12">
                <div class="form-group">
                  <label :for="'documento_' + entidad"
                    >Documento <span class="text-danger">*</span></label
                  >
                  <input
                    type="text"
                    :id="'documento_' + entidad"
                    class="form-control form-control-sm"
                    name="documento"
                    autocomplete="off"
                    @keypress="soloNumeros($event)"
                    :value="cita != null ? cita.documento : documento"
                    minlength="8"
                    maxlength="9"
                    @keyup.enter="busquedaCliente('E')"
                    @change="busquedaCliente('C')"
                  />
                </div>
              </div>

              <div v-if="mostrarTipo" class="col-md-3 col-xs-12">
                <div class="form-group">
                  <label :for="'apellidos_' + entidad"
                    >Apellidos <span class="text-danger">*</span></label
                  >
                  <input
                    type="text"
                    :id="'apellidos_' + entidad"
                    class="form-control form-control-sm"
                    name="apellidos"
                    @keypress="soloLetras($event)"
                    :value="cliente != null ? cliente.apellidos : ''"
                  />
                </div>
              </div>

              <div v-if="mostrarTipo" class="col-md-3 col-xs-12">
                <div class="form-group">
                  <label :for="'nombres_' + entidad"
                    >Nombres <span class="text-danger">*</span></label
                  >
                  <input
                    type="text"
                    :id="'nombres_' + entidad"
                    :value="cliente != null ? cliente.nombres : ''"
                    class="form-control form-control-sm"
                    name="nombres"
                    @keypress="soloLetras($event)"
                  />
                </div>
              </div>

              <div v-if="!mostrarTipo" class="col-md-4 col-xs-12">
                <div class="form-group">
                  <label :for="'razonSocial_' + entidad"
                    >Razon Social <span class="text-danger">*</span></label
                  >
                  <input
                    type="text"
                    :value="cliente != null ? cliente.razonSocial : ''"
                    :id="'razonSocial_' + entidad"
                    class="form-control form-control-sm"
                    name="razonSocial"
                    @keypress="soloLetras($event)"
                  />
                </div>
              </div>

              <div class="col-md-2 col-xs-12">
                <div class="form-group">
                  <label :for="'telefono_' + entidad"
                    >Teléfono <span class="text-danger">*</span></label
                  >
                  <input
                    type="text"
                    :id="'telefono_' + entidad"
                    class="form-control form-control-sm"
                    name="telefono"
                    @keypress="soloNumeros($event)"
                    minlength="6"
                    maxlength="9"
                    :value="cliente != null ? cliente.telefono : ''"
                  />
                </div>
              </div>

              <div class="col-md-3 col-xs-12">
                <div class="form-group">
                  <label :for="'correo_' + entidad"
                    >Correo Electrónico
                    <span class="text-danger">*</span></label
                  >
                  <input
                    type="email"
                    :id="'correo_' + entidad"
                    class="form-control form-control-sm"
                    name="correo"
                    :value="cliente != null ? cliente.correoElectronico : ''"
                  />
                </div>
              </div>

              <div class="col-md-12" :id="'data-cliente_' + entidad"></div>
            </div>
            <hr />
            <div class="row mt-1 mx-1">
              <div class="col-md-7 col-xs-12">
                <div class="row">
                  <div class="col-md-12 col-xs-12">
                    <h6 class="text-muted title">
                      <strong>DATOS DEL AUTO</strong>
                    </h6>
                  </div>

                  <div class="col-md-3 col-xs-12">
                    <label :for="'select_marca_' + entidad"
                      >Marca <span class="text-danger">*</span></label
                    >
                    <select
                      class="custom-select custom-select-sm"
                      name="select_marca"
                      :id="'select_marca_' + entidad"
                      @change="agregarMarca"
                    >
                      <option disabled="" value="" selected="">
                        Seleccione
                      </option>
                      <option
                        v-for="f in marcas"
                        :selected="marcaAutoId == f.id"
                        :key="f.id"
                        :value="f.id"
                      >
                        {{ f.nombre }}
                      </option>
                      <option value="otro" :selected="marcaAutoId == null">
                        Indique otra
                      </option>
                    </select>
                  </div>

                  <div
                    :class="
                      (marcaAuto == null ? 'ocultar' : '') + ' col-md-3 col-xs-12'
                    "
                    :id="'marca_ocultar_' + entidad"
                  >
                    <div class="form-group">
                      <label :for="'marca_' + entidad"
                        >Marca <span class="text-danger">*</span></label
                      >
                      <input
                        type="text"
                        :id="'marca_' + entidad"
                        :value="marcaAuto"
                        class="form-control form-control-sm"
                        name="marca"
                        maxlength="255"
                      />
                    </div>
                  </div>

                  <div class="col-md-3 col-xs-12">
                    <div class="form-group">
                      <label :for="'modelo_' + entidad"
                        >Modelo <span class="text-danger">*</span></label
                      >
                      <input
                        type="text"
                        :id="'modelo_' + entidad"
                        :value="modelo"
                        class="form-control form-control-sm"
                        name="modelo"
                        maxlength="255"
                      />
                    </div>
                  </div>

                  <div class="col-md-3 col-xs-12">
                    <div class="form-group">
                      <label :for="'anio_' + entidad"
                        >Año <span class="text-danger">*</span></label
                      >
                      <input
                        type="number"
                        min="1800"
                        :value="cita != null ? cita.anio : '2000'"
                        :id="'anio_' + entidad"
                        class="form-control form-control-sm text-center"
                        name="anio"
                      />
                    </div>
                  </div>
                </div>

                <div class="row">
                  <div class="col-md-3 col-xs-12">
                    <div class="form-group">
                      <label :for="'placa_' + entidad"
                        >Placa <span class="text-danger">*</span></label
                      >
                      <input
                        type="text"
                        :id="'placa_' + entidad"
                        class="form-control form-control-sm text-center"
                        :value="placa"
                        name="placa"
                        maxlength="10"
                      />
                    </div>
                  </div>

                  <div class="col-md-3 col-xs-12">
                    <div class="form-group">
                      <label :for="'kilometraje_' + entidad"
                        >Kilometraje <span class="text-danger">*</span></label
                      >
                      <input
                        type="number"
                        step="0.1"
                        :value="kilometraje"
                        :id="'kilometraje_' + entidad"
                        class="form-control form-control-sm text-center"
                        name="kilometraje"
                      />
                    </div>
                  </div>

                  <div class="col-md-3 col-xs-12">
                    <div class="form-group">
                      <label :for="'vin_' + entidad"
                        >VIN <span class="text-danger">*</span></label
                      >
                      <input
                        type="text"
                        :id="'vin_' + entidad"
                        class="form-control form-control-sm text-center"
                        :value="vin"
                        name="vin"
                        maxlength="20"
                      />
                    </div>
                  </div>
                </div>
                <hr />
                <div class="row">
                  <div class="col-md-12 col-xs-12">
                    <h6 class="text-muted title">
                      <strong>DATOS DE CITA</strong>
                    </h6>
                  </div>

                  <div class="col-md-3 col-xs-12">
                    <label :for="'select_tipo_' + entidad"
                      >T. de Servicio <span class="text-danger">*</span></label
                    >
                    <select
                      class="custom-select custom-select-sm"
                      name="tipoServicio"
                      :id="'select_tipo_' + entidad"
                    >
                      <option
                        v-for="f in tipoServicio"
                        :key="f.id"
                        :value="f.id"
                        :selected="tipoServicioId == f.id"
                      >
                        {{ f.nombre }}
                      </option>
                    </select>
                  </div>

                  <div class="col-md-3 col-xs-12">
                    <div class="form-group">
                      <label :for="'fecha_' + entidad"
                        >Fecha <span class="text-danger">*</span></label
                      >
                      <input
                        type="date"
                        :value="fecha"
                        :id="'fecha_' + entidad"
                        class="form-control form-control-sm text-center"
                        name="fecha"
                      />
                    </div>
                  </div>

                  <div class="col-md-3 col-xs-12">
                    <div class="form-group">
                      <label :for="'hora_' + entidad"
                        >Hora <span class="text-danger">*</span></label
                      >
                      <input
                        type="time"
                        :value="hora"
                        :id="'hora_' + entidad"
                        class="form-control form-control-sm text-center"
                        name="hora"
                      />
                    </div>
                  </div>

                  <div class="col-md-3 col-xs-12">
                    <div class="form-group">
                      <label :for="'duracion_' + entidad"
                        >Duración <small class="text-muted">(en min.)</small>
                        <span class="text-danger">*</span></label
                      >
                      <input
                        readOnly=""
                        type="number"
                        :value="cita != null ? cita.duracion : '30'"
                        :id="'duracion_' + entidad"
                        class="form-control form-control-sm text-center"
                        name="duracion"
                        min="10"
                      />
                    </div>
                  </div>
                </div>

                <div class="row">
                  <div class="col-md-3 col-xs-12">
                    <label :for="'select_con_cita_' + entidad"
                      >¿Llegó con Cita?
                      <span class="text-danger">*</span></label
                    >
                    <select
                      class="custom-select custom-select-sm"
                      name="con_cita"
                      :id="'select_con_cita_' + entidad"
                    >
                      <option
                        v-for="f in llegoCita"
                        :key="f.id"
                        :value="f.id"
                        :selected="conCita == f.id"
                      >
                        {{ f.nombre }}
                      </option>
                    </select>
                  </div>

                  <div class="col-md-3 col-xs-12">
                    <label :for="'select_con_soat_' + entidad"
                      >¿Con SOAT?
                      <span class="text-danger">*</span></label
                    >
                    <select
                      class="custom-select custom-select-sm"
                      name="con_soat"
                      :id="'select_con_soat_' + entidad"
                    >
                      <option disabled="" value="" selected="">
                        Seleccione
                      </option>
                      <option
                        v-for="f in llegoCita"
                        :key="f.id"
                        :value="f.id"
                        :selected="conSoat == f.id"
                      >
                        {{ f.nombre }}
                      </option>
                    </select>
                  </div>

                  <div class="col-md-3 col-xs-12">
                    <label :for="'select_con_seguro_veh_' + entidad"
                      >¿Seguro Vehicular?
                      <span class="text-danger">*</span></label
                    >
                    <select
                      class="custom-select custom-select-sm"
                      name="con_seguro"
                      :id="'select_con_seguro_veh_' + entidad"
                    >
                      <option disabled="" value="" selected="">
                        Seleccione
                      </option>
                      <option
                        v-for="f in llegoCita"
                        :key="f.id"
                        :value="f.id"
                        :selected="conSeguro == f.id"
                      >
                        {{ f.nombre }}
                      </option>
                    </select>
                  </div>


                  <div class="col-md-3 col-xs-12" v-if="disabledSituacion">
                    <label :for="'select_situacion_' + entidad"
                      >Situación <span class="text-danger">*</span></label
                    >
                    <select
                      class="custom-select custom-select-sm"
                      name="situacion"
                      :id="'select_situacion_' + entidad"
                    >
                      <option
                        v-for="f in situaciones"
                        :key="f.id"
                        :value="f.id"
                        :selected="situacionId == f.id"
                      >
                        {{ f.nombre }}
                      </option>
                    </select>
                  </div>
                </div>

                <div class="row mt-1">
                  <div class="col-md-11 col-xs-12">
                    <div class="form-group">
                      <label :for="'indicaciones_' + entidad"
                        >Indicaciones para su Reparación
                        <span class="text-danger">*</span></label
                      >
                      <textarea
                        :id="'indicaciones_' + entidad"
                        class="form-control form-control-sm no-resize"
                        rows="2"
                        name="indicaciones"
                        v-text="indicaciones"
                      ></textarea>
                    </div>
                  </div>
                </div>
              </div>
              <hr />

              <div
                class="col-md-5 col-xs-12"
                style="border-left: 1px solid rgba(0, 0, 0, 0.1)"
              >
                <div class="row">
                  <div class="col-md-12 col-xs-12">
                    <h6 class="text-muted title">
                      <strong>ASIGNAR PERSONAL</strong>
                    </h6>
                  </div>
                </div>

                <div class="row">
                  <div class="col-md-8 col-xs-12">
                    <div class="form-group">
                      <label :for="'personal_bq_' + entidad"
                        >Tipo de Usuario/Personal
                        <span class="text-danger">*</span></label
                      >
                      <input
                        type="text"
                        :id="'personal_bq_' + entidad"
                        placeholder="Presione Enter para Buscar"
                        class="form-control form-control-sm"
                        name="personal_bq"
                        maxlength="255"
                        @keyup.enter="cargarPersonal"
                      />
                    </div>
                  </div>
                  <div class="col-md-2 mt-2">
                    <button
                      class="btn btn-sm btn-info"
                      type="button"
                      style="margin-top: 5px"
                      data-bs-toggle="tooltip"
                      data-bs-placement="top"
                      title="Buscar"
                      @click="cargarPersonal"
                    >
                      <i class="mdi mdi-magnify icon-size"></i>
                    </button>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-12 col-xs-12">
                    <div
                      class="table-responsive"
                      id="tabla-buscar"
                      style="height: 180px; overflow-y: scroll"
                    >
                      <table
                        class="table table-striped table-sm table-hover mb-0"
                      >
                        <thead>
                          <tr style="background: #062fa0; border-radius: 5px">
                            <!-- <th class="text-left" style="color:#fff;">#</th> -->
                            <th class="text-center" style="color: #fff">
                              Personal
                            </th>
                            <!-- <th class="text-center" style="color:#fff;">Tipo </th> -->
                            <th class="text-center" style="color: #fff">
                              Descripción
                            </th>
                            <!-- <th class="text-center" style="color:#fff;">Fecha</th> -->
                            <th class="text-center" style="color: #fff">
                              Fecha/Duración
                            </th>
                          </tr>
                        </thead>
                        <tbody style="height: 150px">
                          <tr v-if="!personas.length">
                            <td class="text-left text-danger" colspan="6">
                              <strong
                                >No se Encontraron Resultados en su
                                Búsqueda</strong
                              >
                            </td>
                          </tr>

                          <tr v-for="(p, index) in personas" :key="index">
                            <!-- <td class="text-left" v-text="((index+1)<10?'0'+(index+1):(index+1))" ></td> -->
                            <td class="text-center">
                              <small v-text="p.personal"></small>
                            </td>
                            <!-- <td class="text-center"><small><strong v-text="p.tipo"></strong></small></td> -->
                            <td class="text-center">
                              <p style="text-align: justify">
                                <!-- <small><strong>Tipo: </strong><span v-text="p.tipo"></span></small> -->
                                <small
                                  ><strong>Orden: </strong
                                  ><span v-text="p.servicio"></span>
                                </small>
                              </p>
                            </td>
                            <!-- <td class="text-center"><small></small></td> -->
                            <td class="text-center">
                              <p style="text-align: justify">
                                <small
                                  ><strong>Fecha: </strong
                                  ><mark
                                    v-text="
                                      p.fecha == null ? 'No Asignado' : p.fecha
                                    "
                                  ></mark
                                ></small>
                                <small
                                  ><strong>Duración: </strong
                                  ><span v-text="p.tiempo + ' min.'"></span
                                ></small>
                              </p>
                            </td>
                          </tr>
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>
              </div>
              <!--<div class="col-md-2 col-xs-12">
                                <div class="form-group">
                                <label for="fecha">Fecha <span class="text-danger">*</span></label>
                                <input type="date" id="fecha" class="form-control text-center" name="fecha">
                                </div>
                            </div>-->
            </div>

            <div class="col-md-12" :id="'data-error-modal_' + entidad"></div>
          </div>
          <div class="modal-footer">
            <button
              type="button"
              class="btn btn-danger btn-sm"
              @click="cerrarModal"
            >
              <i class="mdi mdi-close icon-size"></i> Cancelar
            </button>

            <button
              type="button"
              :disabled="bloqueado"
              @click="
                enviarFormModalAct(
                  'modalCita',
                  entidad,
                  'CIT',
                  null,
                  objCalendar,
                  tipoEnvio
                )
              "
              class="btn btn-success btn-sm"
              :id="'btnEnvio_' + entidad"
            >
              <i class="mdi mdi-check-bold icon-size"></i> {{ btn }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
import axios from "axios";
import { misMixins } from "../../mixins/mixin.js";

export default {
  name: "MantenimientoCita",
  mixins: [misMixins],
  data() {
    return {
      fecha: "",
      hora: "",
      marcas: [],
      numero: "",
      opciones: "",
      url: "/cita",
      token: this.$store.state.token,
      tipo: "Crear",
      id: 0,
      tipos: [
        { id: "B", nombre: "Persona Natural" },
        { id: "F", nombre: "Persona Jurídica" },
      ],
      tipoServicio: [
        { id: "MP", nombre: "Mantenimiento Preventivo" },
        { id: "MC", nombre: "Mantenimiento Correctivo" },
        { id: "L",  nombre: "Lavado" },
        { id: "PB",  nombre: "Programa de Bienvenida" },
        { id: "IA",  nombre: "Instalación de accs" },
        { id: "PD",  nombre: "PDI" },
        { id: "G",  nombre: "Garantía" },
        { id: "C",  nombre: "Campaña" },
        { id: "PP",  nombre: "Planchado y Pintura" },
        { id: "IS",  nombre: "Inspección Seminuevo" },
        { id: "TR",  nombre: "Trabajo repetido" },
        { id: "S",  nombre: "Siniestro" }
      ],
      situaciones: [
        { id: "A", nombre: "Confirmar Cita" },
        { id: "P", nombre: "Pendiente de Confirmación" },
        { id: "C", nombre: "Cancelar Cita" },
      ],
      llegoCita: [
        { id: "S", nombre: "Si" },
        { id: "N", nombre: "No" },
      ],
      mostrarTipo: true,
      disabledSituacion: true,
      documento: "",
      cliente: null,
      marcaAgregar: false,
      objCalendar: null,
      cita: null,
      tipoId: "",
      btn: "Guardar",
      marcaAutoId: "",
      tipoServicioId: "",
      situacionId: "",
      tipoEnvio: 1,
      bloqueado: false,
      entidad: "cita_mant",
      personas: [],
      modelo: "",
      placa: "",
      vin: "",
      kilometraje: "",
      marcaAuto: null,
      conCita: "",
      indicaciones: "",
      conSoat: "",
      conSeguro: "",
      ejecutadoBq: false
    };
  },
  methods: {
    async cargarPersonal() {
      let me = this;
      let busq = document.getElementById("personal_bq_" + me.entidad).value;
      // console.log('busq', busq)
      let response = await axios({
        method: "post",
        url: "/getbusqueda",
        data: {
          busqueda: busq,
          _token: me.token,
        },
      });

      me.modelo = document.getElementById(`modelo_${me.entidad}`).value;
      me.placa = document.getElementById(`placa_${me.entidad}`).value;
      me.kilometraje = document.getElementById(
        `kilometraje_${me.entidad}`
      ).value;

      me.personas = response.data.personas;
    },
    agregarMarca() {
      let me = this;
      let val = document.getElementById("select_marca_" + me.entidad).value;
      let element = document.getElementById("marca_ocultar_" + me.entidad);
      if (val == "otro") {
        element.classList.remove("ocultar");
        document.getElementById("marca_" + me.entidad).focus();
      } else {
        element.classList.add("ocultar");
        document.getElementById("modelo_" + me.entidad).focus();
      }
    },
    cerrarModal () {
      // $('.fade').remove();
      // $('body').removeClass('modal-open');
      // $('.modal-backdrop').remove();
      $('#modalCita').modal('toggle')
      $('#modalCita').css('z-index', '-1')
      $('#header-sec').css('z-index','')
      $('#aside-sec').css('z-index','')   
      document.getElementById(`formEnvModal_${this.entidad}`).reset();
    },
    async showModal(attr, object) {
      let me = this;
      let response = await axios.get("/marcasauto");
      me.marcas = response.data.marcasauto;
      document.getElementById("data-cliente_" + me.entidad).innerHTML = "";
      document.getElementById("data-error-modal_" + me.entidad).innerHTML = "";
      let element = document.getElementById("marca_ocultar_" + me.entidad);
      element.classList.add("ocultar");

      document.getElementById("indicaciones_" + me.entidad).value = "";
      document.getElementById("select_marca_" + me.entidad).selectedIndex = 0;
      document.getElementById("select_tipo_" + me.entidad).selectedIndex = 0;
      document.getElementById("select_con_cita_" + me.entidad).selectedIndex = 0;
      document.getElementById("select_tipodocumento_" + me.entidad).selectedIndex = 0;
     
      
      $("#modalCita").css("z-index", "-1");
      me.id = attr;
      me.objCalendar = object;
      if (me.id > 0) {
        me.disabledSituacion = true;
        let response = await axios({
          method: "post",
          url: "/getcita/" + me.id,
          data: {
            _token: me.token,
          },
        });

        if (response.data.estado) {
          me.cita = response.data.cita;
          me.numero = me.cita.numeroc;
          me.cliente = {
            apellidos: me.cita.apellidos,
            nombres: me.cita.nombres,
            razonSocial: me.cita.razonSocial,
            telefono: me.cita.telefono,
            correoElectronico: me.cita.correoElectronico,
          };
          me.tipoId = me.cita.documento.length == 8 ? "B" : "F";
          me.fecha = me.cita.fecha;
          me.hora = me.cita.hora;
          me.modelo = me.cita.modelo;
          me.placa = me.cita.placa;
          me.vin  = me.cita.vin;
          me.kilometraje = me.cita.kilometraje;
          me.marcaAutoId = me.cita.idMarcaAuto;
          me.marcaAuto = me.cita.marcaAuto;
          me.tipoServicioId = me.cita.tipoServicio;
          me.btn = "Actualizar";
          me.mostrarTipo = me.tipoId == "B" ? true : false;
          me.situacionId = me.cita.situacion;
          // alert(me.cita.situacion)
          me.tipoEnvio = 2;
          me.bloqueado = response.data.bloqueado;
          me.documento = "";
          me.conCita = me.cita.con_cita;
          me.conSoat = me.cita.con_soat;
          me.conSeguro = me.cita.con_seguro;
         
          me.indicaciones = me.cita.indicaciones;
          document.getElementById("indicaciones_" + me.entidad).value = me.indicaciones;
        }
      } else {
        let response2 = await axios.get("/obtenercorrelativocita");
        me.numero = response2.data.numero;

        me.disabledSituacion = false;
        me.marcaAutoId = "";
        me.tipoServicioId = "";
        me.tipoId = "";
        me.cita = null;
        me.cliente = null;
        me.modelo = "";
        me.placa = "";
        me.kilometraje = "";
        me.vin = "";
        me.marcaAuto = null;
        me.conCita = "";
        me.conSeguro = "";
        me.conSoat = "";
        me.documento = "";
        me.indicaciones = "";
        // console.log(me.objCalendar);
        if (me.objCalendar != null) {
          if (me.objCalendar.startStr == undefined) {
            me.objCalendar = null;
          }
        }
        me.fecha =
          me.objCalendar == null
            ? me.obtenerFechaActual()
            : me.objCalendar.startStr;
        me.hora = this.obtenerHoraActual();
        me.tipoServicioId = "";
        me.situacionId = "";
        me.btn = "Guardar";
        me.tipoEnvio = 1;
        me.bloqueado = false;
      }
      // alert(me.hora)
      // document.getElementById('fecha').value = me.fecha
      // alert(object.startStr)
      // me.arreglo = response.data.opciones
      // me.arreglo2 = response.data.encabezados
      // me.opciones = response.data.opciones02
      // this.$store.state.mostrarModal = !this.$store.state.mostrarModal
      $("#modalCita").modal({ backdrop: "static", show: true, keyboard: false });
      $("#modalCita").css("z-index", "1500");
      $(".modal-backdrop").css("z-index", "1");
      $('#header-sec').css('z-index','1')
      $('#aside-sec').css('z-index','1')
      // }
      // }
      // $('#exampleModal').on('shown.bs.modal', function () {
      //     $('#myInput').trigger('focus')
      // })
    },
    tipoVenta() {
      let me = this;
      let val = document.getElementById(
        "select_tipodocumento_" + me.entidad
      ).value;
      let element = document.getElementById("documento_" + me.entidad);
      if (val == "F") {
        element.setAttribute("minlength", "11");
        element.setAttribute("maxlength", "11");
        me.mostrarTipo = false;
        // me.subtotal = Math.round((parseFloat(me.total/1.18)*100))/100
        // me.igv = Math.round((parseFloat(me.total - me.subtotal)*100))/100
      } else {
        element.setAttribute("minlength", "8");
        element.setAttribute("maxlength", "9");
        me.mostrarTipo = true;
        // me.igv = 0
        // me.subtotal = me.total
      }
      element.value = "";
      element.focus();

      if (me.cita != null) {
        me.cita.documento = "";
      }
      // document.getElementById('cliente').value = ''
    },
    async busquedaCliente(type) {
      let me = this;
      let val = document.getElementById(
        "select_tipodocumento_" + me.entidad
      ).value;
      document.getElementById("data-cliente_" + me.entidad).innerHTML = "";
      if (type == 'E') { me.ejecutadoBq = true; }
      if (type == 'C' && me.ejecutadoBq) {
        me.ejecutadoBq = false
      } else {
        me.ejecutadoBq = true
      }

      if (me.ejecutadoBq) {
        if (val == "") {
          document.getElementById("data-cliente_" + me.entidad).innerHTML =
            '<div style = "margin-top:10px;"><div class="alert alert-danger text-left alert-dismissible" role="alert"><button type = "button" class="close" data-dismiss = "alert">&times;</button><strong>Seleccione Tipo de Documento Antes de Seguir...</strong></a></div ></div>';
        } else {
          let valorDoc = document.getElementById("documento_" + me.entidad).value;
          if (valorDoc.length == 8 || valorDoc.length == 9 || valorDoc.length == 11) {
            document.getElementById("data-cliente_" + me.entidad).innerHTML =
              '<div style = "margin-top:10px;"><div class="alert alert-warning text-left alert-dismissible" role="alert"><button type = "button" class="close" data-dismiss = "alert">&times;</button><strong>Espere...</strong></a></div ></div>';

            me.documento = valorDoc;
            if (me.cita != null) {
              me.cita.documento = valorDoc;
            } else {
              me.documento = valorDoc;
            }
            // let response = await axios.get('/obtenercliente/'+valorDoc+'/'+val)
            let response = await axios({
              method: "post",
              url: "/getcliente/" + me.documento,
              data: {
                _token: me.token,
              },
            });

            if (!response.data.estado) {
              let res = await me.consultarApi(
                me.documento,
                me.documento.length == 8 ? 1 : 2
              );
              let cl = res.data;
              console.log("resp", cl);
              if (cl.success) {
                let client = cl.data;
                // console.log(cl);
                if (me.documento.length == 11) {
                  me.cliente = {
                    razonSocial: client.nombre_o_razon_social,
                    direccion: client.direccion_completa,
                  };
                } else {
                  me.cliente = {
                    apellidos:
                      client.apellido_paterno + " " + client.apellido_materno,
                    nombres: client.nombres,
                  };
                }
              }
            } else {
              me.cliente = null;
            }

            let bandValidacion = true;
            window.setTimeout(function () {
              document.getElementById("data-cliente_" + me.entidad).innerHTML =
                "";
              if (response.data.estado) {
                me.cliente = response.data.cliente;
                if (
                  me.cliente.apellidos == null &&
                  me.cliente.nombres == null &&
                  me.cliente.razonSocial == null
                ) {
                  bandValidacion = false;
                }

                if (!bandValidacion) {
                  document.getElementById(
                    "data-cliente_" + me.entidad
                  ).innerHTML =
                    '<div style = "margin-top:10px;"><div class="alert alert-warning text-left alert-dismissible" role="alert"><button type = "button" class="close" data-dismiss = "alert">&times;</button><strong>Documento no encontrado, Registre por favor...</strong></a></div ></div>';
                }
              } else {
                // me.cliente = null
                // console.log('cliente', me.cliente);
                if (me.cliente == null) {
                  document.getElementById(
                    "data-cliente_" + me.entidad
                  ).innerHTML =
                    '<div style = "margin-top:10px;"><div class="alert alert-warning text-left alert-dismissible" role="alert"><button type = "button" class="close" data-dismiss = "alert">&times;</button><strong>Documento no encontrado, Registre por favor...</strong></a></div ></div>';
                }
              }
            }, 500);
          } else {
            document.getElementById("data-cliente_" + me.entidad).innerHTML =
              '<div style = "margin-top:10px;"><div class="alert alert-warning text-left alert-dismissible" role="alert"><button type = "button" class="close" data-dismiss = "alert">&times;</button><strong>El Documento debe ser DNI o RUC</strong></a></div ></div>';
          }
        }
      }
    },
  },
  created() {
    let me = this;
  },
  activated: function () {
    // this.$route.meta.auth = localStorage.getItem('autenticado')
    this.$store.state.mostrarModal = !this.$store.state.mostrarModal;
    this.personas = [];
  },
  deactivated: function () {},
  mounted() {},
  beforeDestroy: function () {
    // let me  = this
  },
};
</script>
<style scoped>
.modal-header {
  border-bottom: 1px solid #f2f2f2;
}
.modal-footer {
  border-top: 1px solid #f2f2f2;
}
.ocultar {
  display: none;
}
input[type="checkbox"] {
  /* remove standard background appearance */
  -webkit-appearance: none;
  -moz-appearance: none;
  appearance: none;
  /* create custom radiobutton appearance */
  display: inline-block;
  width: 15px;
  height: 15px;
  padding: 2px;
  /* background-color only for content */
  background-clip: content-box;
  border: 2px solid #bbbbbb;
  background-color: #e7e6e7;
  border-radius: 30%;
  vertical-align: middle;
  position: relative;
  top: -2px;
}

/* appearance for checked radiobutton */
input[type="checkbox"]:checked {
  background-color: teal;
  cursor: pointer;
}
.no-resize {
  resize: none;
}
</style>
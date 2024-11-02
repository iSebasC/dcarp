<template>
  <div class="content-wrapper" :id="'sec_' + entidad">
    <div class="content-header row">
      <div class="content-header-left col-md-6 col-12 mb-2">
        <div class="row breadcrumbs-top d-block">
          <div class="breadcrumb-wrapper col-12">
            <ol class="breadcrumb">
              <li class="breadcrumb-item">
                <router-link tag="a" to="/">Inicio</router-link>
              </li>
              <li class="breadcrumb-item">
                <router-link tag="a" to="/compra">Compras</router-link>
              </li>
              <li class="breadcrumb-item active">{{ accion }} Compra</li>
            </ol>
          </div>
        </div>
        <h4 class="content-header-title mb-0 mt-1">{{ accion }} Compra</h4>
      </div>
    </div>
    <div class="content-body">
      <!-- Basic form layout section start -->
      <section id="basic-form-layouts">
        <div class="row match-height">
          <div class="col-md-12 col-xs-12">
            <div class="card">
              <!-- <div class="card-header pb-1">
                <h4 class="card-title" id="basic-layout-form">
                  <i class="la la-money-check-alt"></i> Datos de la Compra
                </h4>
              </div> -->

              <div class="card-content collapse show">
                <div class="card-body">
                  <form
                    class="form"
                    :id="'formEnv_' + entidad"
                    method="POST"
                    action="/guardarcompra"
                  >
                    <input
                      type="hidden"
                      :value="this.$store.state.token"
                      name="_token"
                    />
                    <div class="form-body">
                      <div class="row">
                        <div class="col-md-4 col-xs-12">
                          <div class="row">
                            <div class="col-md-6 col-xs-12">
                              <div class="form-group">
                                <label :for="'fecha_' + entidad"
                                  >Fecha Doc.
                                  <span class="text-danger">*</span></label
                                >
                                <input
                                  type="date"
                                  :id="'fecha_' + entidad"
                                  class="
                                    form-control form-control-sm
                                    text-center
                                  "
                                  name="fecha"
                                />
                              </div>
                            </div>
                            <div class="col-md-6" style="margin-top: 30px">
                              <input
                                type="checkbox"
                                name="chkIgv"
                                :id="'chk_igv_' + entidad"
                                @change="isCheckIgv()"
                                :checked="chkIgv"
                              />
                              <label
                                class="text-muted"
                                :for="'chk_igv_' + entidad"
                                ><small>¿Es afecto a igv?</small></label
                              >
                            </div>
                          </div>

                          <div class="row">
                            <div class="col-md-6 col-xs-12">
                              <div class="form-group">
                                <label :for="'documento_' + entidad"
                                  >Documento
                                  <span class="text-danger">*</span></label
                                >
                                <input
                                  type="text"
                                  :id="'documento_' + entidad"
                                  class="form-control form-control-sm"
                                  name="documento"
                                  maxlength="11"
                                  minlength="8"
                                  @keypress="soloNumeros($event)"
                                  @keyup.enter="busquedaProveedor"
                                />
                              </div>
                            </div>

                            <div class="col-md-2 col-xs-12">
                              <div class="content-buttons btn-group my-md-3 pt-1">
                                <button
                                  @click="busquedaProveedor"
                                  title="Buscar Proveedor"
                                  class="btn btn-sm btn-info"
                                  type="button"
                                  style="margin-top: 5px"
                                >
                                  <i class="mdi mdi-magnify icon-size"></i>
                                </button>
                                <button
                                  v-if="bandCliente02"
                                  @click="verCliente"
                                  title="Ver Proveedor"
                                  class="btn btn-sm btn-warning"
                                  type="button"
                                  style="margin-top: 5px"
                                >
                                  <i class="mdi mdi-file-eye icon-size"></i>
                                </button>
                                <button
                                  v-if="bandCliente"
                                  @click="agregarCliente"
                                  title="Buscar Proveedor"
                                  class="btn btn-sm btn-danger"
                                  type="button"
                                  style="margin-top: 5px"
                                >
                                  <i class="mdi mdi-plus icon-size"></i>
                                </button>
                              </div>
                            </div>
                          </div>

                          <div class="row">
                            <div class="col-md-12 col-xs-12">
                              <div class="form-group">
                                <label :for="'cliente_' + entidad"
                                  >Proveedor
                                  <span class="text-danger">*</span></label
                                >
                                <input
                                  type="text"
                                  :id="'cliente_' + entidad"
                                  class="form-control form-control-sm"
                                  name="cliente"
                                  maxlength="255"
                                  readonly=""
                                  @keypress="soloLetras($event)"
                                />
                              </div>
                            </div>
                          </div>

                          <div class="row">
                            <div class="col-md-7 col-xs-12">
                              <div class="form-group">
                                <label :for="'select_tipodocumento_' + entidad"
                                  >Tipo de Comprobante
                                  <span class="text-danger">*</span></label
                                >
                                <select
                                  class="custom-select custom-select-sm"
                                  name="tipodocumento"
                                  :id="'select_tipodocumento_' + entidad"
                                  @change="tipoVenta"
                                >
                                  <option value="" disabled="" selected="">
                                    Seleccione
                                  </option>
                                  <option
                                    v-for="f in tipos"
                                    :key="f.id"
                                    :value="f.id"
                                  >
                                    {{ f.nombre }}
                                  </option>
                                </select>
                              </div>
                            </div>

                            <div class="col-md-5 col-xs-12">
                              <div class="form-group">
                                <label :for="'flete_' + entidad"
                                  >Flete
                                  <span class="text-danger">*</span></label
                                >
                                <input
                                  type="number"
                                  :id="'flete_' + entidad"
                                  class="
                                    form-control form-control-sm
                                    text-center
                                  "
                                  name="flete"
                                  :disabled="deshabilitado"
                                  @change="calcularPreciosVenta"
                                  @keyup.enter="calcularPreciosVenta"
                                  step="0.01"
                                  min="0.0"
                                  value="0"
                                />
                              </div>
                            </div>
                          </div>

                          <div class="row">
                            <div class="col-md-7 col-xs-12">
                              <div class="form-group">
                                <label :for="'refdocumento_' + entidad"
                                  >Documento de Referencia
                                  <span class="text-danger">*</span></label
                                >
                                <input
                                  type="text"
                                  :id="'refdocumento_' + entidad"
                                  class="
                                    form-control form-control-sm
                                    text-center
                                  "
                                  name="refdocumento"
                                  placeholder="B1-8596"
                                  maxlength="13"
                                />
                              </div>
                            </div>

                            <div class="col-md-5 col-xs-12">
                              <label :for="'tipoMoneda_' + entidad"
                                >Moneda
                                <span class="text-danger">*</span></label
                              >
                              <select
                                class="custom-select custom-select-sm"
                                @change="capturarMoneda"
                                name="tipoMoneda"
                                :id="'tipoMoneda_' + entidad"
                              >
                                <option value="" disabled="" selected="">
                                  Seleccione
                                </option>
                                <option
                                  v-for="f in tiposCambio"
                                  :key="f.id"
                                  :value="f.id"
                                >
                                  {{ f.nombre }}
                                </option>
                              </select>
                            </div>
                          </div>

                          <div class="row">
                            <div class="col-md-7 col-xs-12">
                              <div class="form-group">
                                <label :for="'fechavencimiento_' + entidad"
                                  >Fecha de Vencimiento
                                  <span class="text-danger">*</span></label
                                >
                                <input
                                  type="date"
                                  :id="'fechavencimiento_' + entidad"
                                  class="
                                    form-control form-control-sm
                                    text-center
                                  "
                                  name="fechavencimiento"
                                />
                              </div>
                            </div>

                            <div class="col-md-5 col-xs-12">
                              <div class="form-group">
                                <label :for="'credito_' + entidad"
                                  >Días de Crédito
                                  <span class="text-danger">*</span></label
                                >
                                <input
                                  type="number"
                                  :id="'credito_' + entidad"
                                  min="0"
                                  value="0"
                                  class="
                                    form-control form-control-sm
                                    text-center
                                  "
                                  name="credito"
                                />
                              </div>
                            </div>
                          </div>

                          <div class="row">
                            <div
                              class="col-md-12"
                              :id="'data-cliente_' + entidad"
                            ></div>
                          </div>
                        </div>

                        <div class="col-md-8 col-xs-12">
                          <div class="row">
                            <div class="col-md-7 col-xs-12">
                              <div class="form-group">
                                <label :for="'descripcion_' + entidad"
                                  >Producto
                                  <span class="text-danger">*</span></label
                                >
                                <input
                                  type="text"
                                  :id="'descripcion_' + entidad"
                                  class="form-control form-control-sm"
                                  name="descripcion"
                                  maxlength="255"
                                  @keyup.enter="cargarProductos"
                                />
                              </div>
                            </div>

                            <div class="col-md-3 col-xs-12">
                              <div class="form-group">
                                <label :for="'cantidad_' + entidad"
                                  >Cantidad
                                  <span class="text-danger">*</span></label
                                >
                                <input
                                  type="number"
                                  :id="'cantidad_' + entidad"
                                  class="
                                    form-control form-control-sm
                                    text-right
                                  "
                                  name="cantidad"
                                  min="0.01"
                                  value="1"
                                  step="0.01"
                                  @keyup.enter="agregarDetalle"
                                />
                              </div>
                            </div>

                            <div class="col-md-2 col-xs-12">
                              <div class="content-buttons btn-group my-md-3 pt-2">
                                <button
                                  @click="agregarDetalle"
                                  title="Agregar Detalle"
                                  class="btn btn-sm btn-danger"
                                  type="button"
                                  style="margin-top: 5px"
                                >
                                  <i class="mdi mdi-plus icon-size"></i>
                                </button>
                              </div>
                            </div>
                          </div>

                          <div class="row">
                            <div class="col-md-12">
                              <div
                                class="table-responsive px-1"
                                style="width:98%;height:300px;margin 2rem auto;"
                                id="tabla-compra"
                              >
                                <table
                                  class="table table-bordered table-sm mb-0"
                                  id="tabla"
                                >
                                  <thead
                                    style="background: #275ee5; color: #fff"
                                  >
                                    <tr>
                                      <th class="text-center">Tipo</th>
                                      <th class="text-center">Descripción</th>
                                    </tr>
                                  </thead>
                                  <tbody>
                                    <tr v-if="!arrProducto.length">
                                      <td
                                        class="text-left text-danger"
                                        colspan="2"
                                      >
                                        <strong
                                          >No se Encontraron Resultados en su
                                          Búsqueda</strong
                                        >
                                      </td>
                                    </tr>

                                    <tr
                                      v-for="(p, index) in arrProducto"
                                      :key="index"
                                    >
                                      <td
                                        class="text-center"
                                        @click="capturarDato(p)"
                                      >
                                        <strong v-text="p.tipo"></strong>
                                      </td>
                                      <td
                                        class="text-center"
                                        v-text="
                                          p.nombre != ''
                                            ? p.nombre +
                                              (p.medida != '-'
                                                ? ', Medida: ' + p.medida
                                                : '') +
                                              (p.tipollanta != '-'
                                                ? ', Tipo de Llanta: ' +
                                                  p.tipollanta
                                                : '') +
                                              (p.modelo != '-'
                                                ? ', Modelo: ' + p.modelo
                                                : '') +
                                              (p.sistema != '-'
                                                ? ' , Sistema: ' + p.sistema
                                                : '')
                                            : p.nombre2
                                        "
                                        @click="capturarDato(p)"
                                      ></td>
                                    </tr>
                                  </tbody>
                                </table>
                              </div>
                            </div>
                          </div>

                          <div class="row">
                            <div
                              class="col-md-12"
                              :id="'data-stock_' + entidad"
                            ></div>
                          </div>
                        </div>
                      </div>
                      <hr />

                      <div class="row">
                        <input
                          type="hidden"
                          name="listDetalles"
                          :id="'listDetalles_' + entidad"
                          :value="listDetalles.join(',')"
                        />
                        <input
                          type="hidden"
                          name="listProductos"
                          :id="'listProductos_' + entidad"
                          :value="listProductos.join(',')"
                        />
                        <input
                          type="hidden"
                          name="listServicios"
                          :id="'listServicios_' + entidad"
                          :value="listServicios.join(',')"
                        />
                        <input
                          type="hidden"
                          name="listAutos"
                          :id="'listAutos_' + entidad"
                          :value="listAutos.join(',')"
                        />

                        <div
                          class="table-responsive px-1"
                          id="tabla-compra-crear"
                          style="width:98%;height:350px;margin 2rem auto;"
                        >
                          <table class="table mb-0">
                            <thead class="table-info">
                              <tr>
                                <th class="text-left" style="width: 20px">#</th>
                                <th class="text-center" style="width: 60px">
                                  Cantidad
                                </th>
                                <th class="text-center" style="width: 280px">
                                  Producto
                                </th>
                                <th class="text-center" style="width: 50px">
                                  P. Compra
                                </th>
                                <th class="text-center" style="width: 50px">
                                  P. Venta
                                </th>
                                <th class="text-center" style="width: 50px">
                                  Sub Total
                                </th>
                                <th
                                  class="text-center"
                                  style="width: 30px"
                                ></th>
                              </tr>
                            </thead>
                            <tbody style="height: 250px">
                              <tr v-for="(p, index) in detalles" :key="index">
                                <td
                                  style="width: 20px"
                                  class="text-left"
                                  v-text="
                                    index + 1 < 10
                                      ? '0' + (index + 1)
                                      : index + 1
                                  "
                                ></td>
                                <td
                                  style="width: 60px"
                                  class="text-center px-1"
                                >
                                  <input
                                    type="number"
                                    v-focus-on-create
                                    step="0.01"
                                    class="
                                      form-control form-control-sm
                                      text-center
                                    "
                                    :name="'txtcantidad' + p.id"
                                    :id="'txtcantidad' + p.id"
                                    :value="p.cantidad"
                                    min="0.01"
                                    @keyup.enter="calcularTotalItem(p.id)"
                                    @change="calcularTotalItem(p.id)"
                                  />
                                </td>
                                <td style="width: 280px" class="text-center">
                                  <textarea
                                    type="text"
                                    style="resize: none"
                                    class="
                                      form-control form-control-sm
                                      no-rezise
                                    "
                                    :name="'txtproducto' + p.id"
                                    :id="'txtproducto' + p.id"
                                    v-text="p.descripcion"
                                    rows="5"
                                    @change="calcularTotalItem(p.id)"
                                  ></textarea>
                                  <input
                                    type="hidden"
                                    :name="'productoid' + p.id"
                                    :id="'productoid' + p.id"
                                    :value="p.id"
                                  />
                                  <input
                                    type="hidden"
                                    :name="'tipo' + p.id"
                                    :id="'tipo' + p.id"
                                    :value="p.tipo"
                                  />
                                </td>
                                <td
                                  style="width: 50px"
                                  class="text-center px-1"
                                >
                                  <input
                                    type="number"
                                    step="0.01"
                                    class="
                                      form-control form-control-sm
                                      text-center
                                    "
                                    :name="'txtprecio' + p.id"
                                    min="0"
                                    :id="'txtprecio' + p.id"
                                    placeholder="P. Compra"
                                    @keyup.enter="calcularTotalItem(p.id)"
                                    @change="calcularTotalItem(p.id)"
                                  />
                                </td>
                                <td
                                  style="width: 50px"
                                  class="text-center px-1"
                                >
                                  <input
                                    type="number"
                                    step="0.01"
                                    class="
                                      form-control form-control-sm
                                      text-center
                                    "
                                    :name="'txtprecioventa' + p.id"
                                    min="0"
                                    :id="'txtprecioventa' + p.id"
                                    :value="p.precioventa"
                                    placeholder="P. Venta"
                                    @blur="calcularPrecioVenta(p.id)"
                                    readonly=""
                                  />
                                </td>
                                <td
                                  style="width: 50px"
                                  class="text-center px-1"
                                >
                                  <input
                                    type="text"
                                    class="
                                      form-control form-control-sm
                                      text-right
                                    "
                                    :name="'txtsubtototal' + p.id"
                                    :id="'txtsubtototal' + p.id"
                                    :value="
                                      Math.round(p.precio * p.cantidad * 100) /
                                      100
                                    "
                                    readonly=""
                                  />
                                  <input
                                    type="hidden"
                                    :name="'txtporcentaje' + p.id"
                                    :id="'txtporcentaje' + p.id"
                                    :value="p.porcentaje"
                                  />
                                </td>

                                <td style="width: 30px">
                                  <a
                                    href="javascript:void(0)"
                                    class="btn-danger btn-sm"
                                    @click="eliminar(p.id, p.tipo)"
                                    title="Eliminar"
                                  >
                                    <i class="mdi mdi-minus-thick icon-size"></i>
                                  </a>
                                </td>
                              </tr>
                            </tbody>
                            <tfoot class="table-danger">
                              <tr>
                                <th colspan="2"></th>
                                <th>
                                  SUB TOTAL: <span v-text="tipo"></span>
                                  <span
                                    class="text-right"
                                    v-text="subtotal"
                                  ></span
                                  ><input
                                    type="hidden"
                                    name="subtotalDoc"
                                    :value="subtotal"
                                  />
                                </th>
                                <th colspan="2">
                                  IGV: <span v-text="tipo"></span>
                                  <span class="text-right" v-text="igv"></span
                                  ><input
                                    type="hidden"
                                    name="igvDoc"
                                    :value="igv"
                                  />
                                </th>
                                <th colspan="2">
                                  TOTAL: <span v-text="tipo"></span>
                                  <span class="text-right" v-text="total"></span
                                  ><input
                                    type="hidden"
                                    name="totalDoc"
                                    :id="'totalDoc_' + entidad"
                                    :value="total"
                                  />
                                </th>
                              </tr>
                            </tfoot>
                          </table>
                        </div>
                      </div>
                      <div class="row">
                        <div
                          class="col-md-12"
                          :id="'data-error_' + entidad"
                        ></div>
                      </div>
                    </div>
                    <div class="form-actions ml-1">
                      <button
                        type="button"
                        @click="enviarFormDetalles(url, entidad, 'C')"
                        :id="'btnEnvio_' + entidad"
                        class="btn btn-sm btn-success"
                      >
                        <i class="mdi mdi-check-bold icon-size"></i> Guardar
                      </button>
                      <button
                        type="button"
                        @click="atras(url)"
                        class="btn btn-danger btn-sm mr-1"
                      >
                        <i class="mdi mdi-close icon-size"></i> Cancelar
                      </button>
                    </div>
                  </form>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>
    </div>
    <Proveedor ref="proveedor" :documento="this.documento"></Proveedor>
  </div>
</template>
<script>
import axios from "axios";
import { misMixins } from "../../mixins/mixin.js";
import Proveedor from "./Proveedor";

export default {
  name: "MantenimientoCompra",
  mixins: [misMixins],
  components: {
    Proveedor,
  },
  data() {
    return {
      accion: "Registrar",
      url: "/compra",
      documento: "",
      bandCliente: false,
      bandCliente02: false,
      arrLocales: [],
      arrAlmacen: [],
      bandTienda: false,
      tiendaId: 0,
      almacenSeleccionado: "",
      almacenId: 0,
      detalles: [],
      total: 0,
      subtotal: 0,
      igv: 0,
      listDetalles: [],
      listProductos: [],
      listServicios: [],
      listAutos: [],
      arrTipos: [],
      arrProducto: [],
      tipo: "",
      producto: "",
      campoBloqueado: false,
      ultElemento: "",
      tiempo: "",
      productos: [],
      deshabilitado: false,
      elementoSeleccionado: {},
      tiendaSeleccionada: {},
      tiposCambio: [
        { id: "S", nombre: "Soles" },
        { id: "D", nombre: "Dólares" },
      ],
      tipos: [
        { id: "B", nombre: "Boleta" },
        { id: "F", nombre: "Factura" },
      ],
      entidad: "compra_mant",
      chkIgv: false,
    };
  },
  methods: {
    isCheckIgv() {
      let elm = document.getElementById(`chk_igv_${this.entidad}`);
      this.chkIgv = elm.checked;
      this.tipoVenta();
    },
    capturarMoneda() {
      let me = this;
      let moneda = document.getElementById("tipoMoneda_" + me.entidad).value;
      if (moneda == "S") {
        me.tipo = moneda + "/";
      } else {
        me.tipo = "$";
      }
    },
    async cargarDatos() {
      let me = this;
      me.accion = "Registrar";
      document.getElementById("documento_" + me.entidad).value = "";
      document.getElementById("cliente_" + me.entidad).value = "";
      document.getElementById("fecha_" + me.entidad).value =
        me.obtenerFechaActual();
      document.getElementById("fechavencimiento_" + me.entidad).value =
        me.obtenerFechaActual();
    },
    async busquedaProveedor() {
      let me = this;
      let valorDoc = document.getElementById("documento_" + me.entidad).value;
      if (valorDoc.length == 8 || valorDoc.length == 11) {
        document.getElementById("data-cliente_" + me.entidad).innerHTML =
          '<div style = "margin-top:10px;"><div class="alert alert-warning text-left alert-dismissible" role="alert"><button type = "button" class="close" data-dismiss = "alert">&times;</button><strong>Espere...</strong></a></div ></div>';

        let response = await axios.get("/obtenerproveedor/" + valorDoc);

        window.setTimeout(function () {
          if (response.data.estado) {
            let razonSocial = "";
            if (response.data.proveedor.tipoDocumento == "PJ") {
              razonSocial = response.data.proveedor.razonSocial;
            } else {
              razonSocial =
                response.data.proveedor.apellidos +
                " " +
                response.data.proveedor.nombres;
            }
            document.getElementById("cliente_" + me.entidad).value =
              razonSocial;
            document.getElementById("data-cliente_" + me.entidad).innerHTML =
              "";
            me.bandCliente02 = true;
            me.bandCliente = false;
          } else {
            me.bandCliente02 = false;
            me.bandCliente = true;
            document.getElementById("cliente_" + me.entidad).value = "";
            document.getElementById("data-cliente_" + me.entidad).innerHTML =
              '<div style = "margin-top:10px;"><div class="alert alert-warning text-left alert-dismissible" role="alert"><button type = "button" class="close" data-dismiss = "alert">&times;</button><strong>Proveedor no Registrado, Antes de Seguir Regístrelo.</strong></a></div ></div>';
          }
        }, 500);
      } else {
        document.getElementById("data-cliente_" + me.entidad).innerHTML =
          '<div style = "margin-top:10px;"><div class="alert alert-warning text-left alert-dismissible" role="alert"><button type = "button" class="close" data-dismiss = "alert">&times;</button><strong>El Documento debe ser DNI o RUC</strong></a></div ></div>';
      }
    },
    async seleccionarProducto(item) {
      let me = this;
      // let arrVal = item.value.split('@')
      // me.stockMax = arrVal[3]

      // document.getElementById('data-stock').innerHTML = '<div style = "margin-top:10px;"><div class="alert alert-success text-left alert-dismissible" role="alert"><button type = "button" class="close" data-dismiss = "alert">&times;</button><strong>Stock Disponible: '+me.stockMax+'</strong></a></div ></div>'

      window.setTimeout(function () {
        document.getElementById("cantidad_" + me.entidad).focus();
      }, 500);
      // document.getElementById('cantidad').focus()
    },
    async cargarProductos() {
      let me = this;
      let descripcion = document.getElementById(
        "descripcion_" + me.entidad
      ).value;
      let productos = await axios({
        method: "post",
        url: "/obtenerproductoscompra",
        data: {
          descripcion: descripcion,
          _token: this.$store.state.token,
        },
      });
      me.arrProducto = productos.data.productos;
    },
    calcularTotalItem(id) {
      let me = this;
      let cantidad = document.getElementById("txtcantidad" + id).value;
      let precio = document.getElementById("txtprecio" + id).value;
      let descripcion = document.getElementById("txtproducto" + id).value;
      // let stock = document.getElementById('stock'+id).value

      document.getElementById("data-stock_" + me.entidad).innerHTML = "";
      // if (cantidad <= parseInt(stock)) {
      me.detalles.forEach((element) => {
        if (element.id == id) {
          element.cantidad = cantidad;
          element.precio = precio;
          element.descripcion = descripcion;
        }
      });
      me.calcularPrecioVenta(id);
      // console.log(me.detalles)
      me.calcularTotal();
      me.calcularPreciosVenta();
      // } else {
      //     document.getElementById('data-stock').innerHTML = '<div style = "margin-top:10px;"><div class="alert alert-danger text-left alert-dismissible" role="alert"><button type = "button" class="close" data-dismiss = "alert">&times;</button><strong>Cantidad Indicada No Disponible</strong></a></div ></div>'
      // }
      // document.getElementById('txtsubtototal'+id).value = ((cantidad*precio)*1000)/1000
      // me.calcularTotal()
    },
    calcularPrecioVenta(id) {
      let me = this;
      let cantidad = parseFloat(
        document.getElementById("txtcantidad" + id).value
      );
      let precio = parseFloat(document.getElementById("txtprecio" + id).value);
      let porcentaje =
        parseFloat(document.getElementById("txtporcentaje" + id).value) / 100;
      let subtotal = parseFloat(
        document.getElementById("txtsubtototal" + id).value
      );
      let total = parseFloat(
        document.getElementById("totalDoc_" + me.entidad).value
      );
      let totalFlete = parseFloat(
        document.getElementById("flete_" + me.entidad).value
      );
      // let stock = document.getElementById('stock'+id).value

      document.getElementById("data-stock_" + me.entidad).innerHTML = "";
      // if (cantidad <= parseInt(stock)) {
      me.detalles.forEach((element) => {
        if (element.id == id) {
          let porc = (precio / total) * cantidad;
          let fleteU = (porc * totalFlete) / cantidad;
          let ax = subtotal / (1 - porcentaje) + fleteU * cantidad;
          element.precioventa = Math.round((ax / cantidad) * 100) / 100;
          // element.cantidad = cantidad
          // element.precio   = precio
          // element.descripcion = descripcion
        }
      });
    },
    calcularPreciosVenta() {
      let me = this;
      // let stock = document.getElementById('stock'+id).value

      document.getElementById("data-stock_" + me.entidad).innerHTML = "";
      // if (cantidad <= parseInt(stock)) {
      me.detalles.forEach((element) => {
        let id = element.id;
        let cantidad = parseFloat(
          document.getElementById("txtcantidad" + id).value
        );
        let precio = parseFloat(
          document.getElementById("txtprecio" + id).value
        );
        let porcentaje =
          parseFloat(document.getElementById("txtporcentaje" + id).value) / 100;
        let subtotal = parseFloat(
          document.getElementById("txtsubtototal" + id).value
        );
        let total = parseFloat(
          document.getElementById("totalDoc_" + me.entidad).value
        );
        let totalFlete = parseFloat(
          document.getElementById("flete_" + me.entidad).value
        );
        // console.log(id+'-'+cantidad+'-'+precio+'-'+porcentaje+'-'+subtotal+'-'+total+'-'+totalFlete)
        // Calculo
        let porc = (precio / total) * cantidad;
        // console.log('Porc', porc)
        let fleteU = (porc * totalFlete) / cantidad;
        // console.log('Flete Unit', fleteU)
        let ax = subtotal / (1 - porcentaje) + fleteU * cantidad;
        // console.log('Precio Sug.', ax)
        element.precioventa = Math.round((ax / cantidad) * 100) / 100;
        // console.log('Precio V.', element.precioventa)
      });
    },
    eliminar(id, tipo) {
      let me = this;
      // alert(tipo)
      if (tipo == "Producto") {
        for (let j = 0; j < me.listProductos.length; j++) {
          let el = me.listProductos[j];
          if (el == id) {
            me.listProductos.splice(j, 1);
          }
        }
      } else if (tipo == "Servicio") {
        for (let j = 0; j < me.listServicios.length; j++) {
          let e = me.listServicios[j];
          if (e == id) {
            me.listServicios.splice(j, 1);
          }
        }
      } else {
        for (let j = 0; j < me.listAutos.length; j++) {
          let e = me.listAutos[j];
          if (e == id) {
            me.listAutos.splice(j, 1);
          }
        }
      }

      for (let i = 0; i < me.detalles.length; i++) {
        let element = me.detalles[i];
        if (element.id == id) {
          if (element.tipo == tipo) {
            me.detalles.splice(i, 1);
          }
        }
      }

      if (me.listProductos.length > 0) {
        me.deshabilitado = true;
      } else {
        me.deshabilitado = false;
      }
      me.calcularPreciosVenta();
      me.calcularTotal();
    },
    calcularTotal() {
      let me = this;
      let acum = 0;
      me.detalles.forEach((element) => {
        if (element.cantidad != "" && element.precio != "") {
          acum += element.cantidad * element.precio;
        }
      });
      me.total = (parseFloat(acum) * 1000) / 1000;
      me.total = Math.round(me.total * 100) / 100;
      // let val = document.getElementById('select_tipodocumento_'+me.entidad).value
      if (me.chkIgv) {
        me.subtotal = Math.round(parseFloat(me.total / 1.18) * 100) / 100;
        me.igv = Math.round(parseFloat(me.total - me.subtotal) * 100) / 100;
      } else {
        me.igv = 0;
        me.subtotal = me.total;
      }
    },
    // colocarFoco (id) {
    //     document.getElementById('txtcantidad'+id).focus()
    // },
    buscarInArray(id, tipo) {
      let me = this;
      let band = true;
      if (tipo == "Auto") {
        if (me.listAutos.indexOf(id) != -1) {
          band = false;
        }
      } else if (tipo == "Producto") {
        if (me.listProductos.indexOf(id) != -1) {
          band = false;
        }
      } else {
        if (me.listServicios.indexOf(id) != -1) {
          band = false;
        }
      }

      return band;
    },
    capturarDato(ref) {
      let me = this;
      let element = document.getElementById("cantidad_" + me.entidad);
      // alert(ref.nombre)
      let rpta = me.buscarInArray(ref.id, ref.tipo);
      document.getElementById("data-stock_" + me.entidad).innerHTML = "";
      me.elementoSeleccionado = ref;
      if (!rpta) {
        document.getElementById("data-stock_" + me.entidad).innerHTML =
          '<div style = "margin-top:10px;"><div class="alert alert-warning text-left alert-dismissible" role="alert"><button type = "button" class="close" data-dismiss = "alert">&times;</button><strong>Producto ya antes Agregado</strong></a></div ></div>';
      }

      element.focus();
    },
    tipoVenta() {
      let me = this;
      // let val = document.getElementById('select_tipodocumento_'+me.entidad).value
      // let element = document.getElementById('documento_'+me.entidad)
      if (me.chkIgv) {
        me.subtotal = Math.round(parseFloat(me.total / 1.18) * 100) / 100;
        me.igv = Math.round(parseFloat(me.total - me.subtotal) * 100) / 100;
      } else {
        me.igv = 0;
        me.subtotal = me.total;
      }
      // element.value = ''
      // document.getElementById('cliente').value = ''
      // element.focus()
    },
    agregarCliente() {
      let me = this;
      me.isValidSession();

      let doc = document.getElementById("documento_" + me.entidad).value;
      me.documento = doc;
      this.$store.state.mostrarModal = !this.$store.state.mostrarModal;
      // alert(this.$store.state.mostrarModal)
      me.$refs.proveedor.showModal(doc, 1);
    },
    verCliente() {
      let me = this;
      me.isValidSession();

      let doc = document.getElementById("documento_" + me.entidad).value;
      me.documento = doc;
      this.$store.state.mostrarModal = !this.$store.state.mostrarModal;
      me.$refs.proveedor.showModal(doc, 2);
    },
    agregarDetalle() {
      let me = this;
      me.isValidSession();
      let ref = me.elementoSeleccionado;
      let rpta = me.buscarInArray(ref.id, ref.tipo);

      let cantidad = document.getElementById("cantidad_" + me.entidad).value;
      document.getElementById("data-stock_" + me.entidad).innerHTML = "";
      let band = false;
      if (rpta) {
        if (ref.tipo == "Producto") {
          let descripcion =
            ref.nombre +
            (ref.medida != "-" ? ", Medida: " + ref.medida : "") +
            (ref.tipollanta != "-"
              ? ", Tipo de Llanta: " + ref.tipollanta
              : "") +
            (ref.modelo != "-" ? ", Modelo: " + ref.modelo : "") +
            (ref.sistema != "-" ? " , Sistema: " + ref.sistema : "");

          if (descripcion == "") {
            descripcion = ref.nombre2;
          }

          var elemento = {
            id: ref.id,
            tipo: ref.tipo,
            descripcion: descripcion,
            cantidad: cantidad,
            precio: 0,
            porcentaje: ref.porcentaje,
            precioventa: 0,
          };
          me.listProductos.push(ref.id);
          band = true;
        } else {
          band = false;
        }

        if (band) {
          me.detalles.push(elemento);
          me.ultElemento = ref.id;
          me.producto = "";
          me.campoBloqueado = true;
          document.getElementById("cantidad_" + me.entidad).value = 1;
          me.calcularTotal();
          me.deshabilitado = true;
        }
      } else {
        document.getElementById("data-stock_" + me.entidad).innerHTML =
          '<div style = "margin-top:10px;"><div class="alert alert-warning text-left alert-dismissible" role="alert"><button type = "button" class="close" data-dismiss = "alert">&times;</button><strong>Producto ya antes Agregado</strong></a></div ></div>';
      }
      // console.log(me.listDetalles)
    },
  },
  created() {
    this.$on("buscarProveedor", function () {
      this.busquedaProveedor();
    });
  },
  activated: function () {
    let me = this;
    me.isValidSession();
    this.$store.state.mostrarModal = !this.$store.state.mostrarModal;
    this.$route.meta.auth = localStorage.getItem("autenticado");
    me.bandCliente = false;
    me.bandCliente02 = false;
    me.bandTienda = false;
    me.campoBloqueado = false;
    me.detalles = [];
    me.listDetalles = [];
    me.tiendaSeleccionada = {};
    me.chkIgv = false;
    document.getElementById(`formEnv_${me.entidad}`).reset();
    // document.getElementById('select_tipodocumento').val = ''
    // document.getElementById('select_tipo_operacion').val = ''
    me.cargarDatos();
    me.calcularTotal();
  },
  mounted() {},
  beforeDestroy: function () {
    let me = this;
    var element = document.getElementById("sec_" + me.entidad);
    element.classList.add("hidden");
    // this.$store.state.mostrarModal = false
  },
};
</script>
<style scoped>
.ocultar {
  display: none;
}
select {
  cursor: pointer;
}

input[type="checkbox"] {
  visibility: hidden;
}
label {
  cursor: pointer;
}
input[type="checkbox"] + label:before {
  border: 1px solid #333;
  content: "\00a0";
  display: inline-block;
  font: 16px/1em sans-serif;
  height: 16px;
  margin: 0.2em 0.28em 0 0;
  padding: 0;
  vertical-align: top;
  width: 16px;
  border-radius: 30%;
}
input[type="checkbox"]:checked + label:before {
  background: blue;
  color: #fff;
  content: "\2713";
  text-align: center;
}
input[type="checkbox"]:checked + label:after {
  font-weight: bold;
}

input[type="checkbox"]:focus + label::before {
  outline: rgb(59, 153, 252) auto 5px;
}

thead {
  position: sticky;
  top: 0;
}
tfoot {
  position: sticky;
  bottom: 0;
}

.table-responsive {
  overflow: auto;
  padding: 0 1rem;
  scrollbar-color: #b46868 rgba(0, 0, 0, 0);
  scrollbar-width: thin;
}
#tabla thead {
  position: sticky;
  top: 0;
}
#tabla tbody tr:hover {
  background: #c6d4f6;
}

#tabla tbody tr {
  cursor: pointer;
}
.no-resize {
  resize: none;
}
</style>
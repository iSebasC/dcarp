<template>
  <div
    class="modal fade"
    id="modalInventario"
    tabindex="-1"
    role="dialog"
    aria-labelledby="modalInventarioLabel"
    aria-hidden="true"
  >
    <div class="modal-dialog modal-xl" role="document">
      <div class="modal-content">
        <form
          class="form"
          :id="'formEnvModal_' + entidad"
          method="POST"
          action="/guardarmovalmacen"
        >
          <div class="modal-header">
            <h5 class="modal-title" id="modalInventarioLabel">
              Almacén: <strong v-text="this.$attrs.lugar"></strong>
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
            <input type="hidden" :value="idalmacen" name="idalmacen" />
            <input
              type="hidden"
              :value="this.$store.state.token"
              name="_token"
            />
            <div class="row">
              <div class="col-md-4 col-xs-12">
                <div class="row">
                  <div class="col-md-6 col-xs-12">
                    <div class="form-group">
                      <label :for="'fecha_' + entidad"
                        >Fecha <span class="text-danger">*</span></label
                      >
                      <input
                        type="date"
                        :id="'fecha_' + entidad"
                        class="form-control form-control-sm text-center"
                        name="fecha"
                      />
                    </div>
                  </div>

                  <div class="col-md-6 col-xs-12">
                    <label :for="'select_tipo_operacion_' + entidad"
                      >Tipo de Operación
                      <span class="text-danger">*</span></label
                    >
                    <select
                      class="custom-select custom-select-sm"
                      :disabled="campoBloqueado"
                      :id="'select_tipo_operacion_' + entidad"
                      name="tipoOperacion"
                      @change="capturarTipoOperacion"
                    >
                      <option value="" disabled="" selected="">
                        Seleccione
                      </option>
                      <option
                        v-for="f in tipoMovimiento"
                        :key="f.id"
                        :value="f.id"
                      >
                        {{ f.descripcion }}
                      </option>
                    </select>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-12">
                    <div class="form-group">
                      <label :for="'observacion_' + entidad"
                        >Observaciones
                        <span class="text-danger">*</span></label
                      >
                      <textarea
                        :id="'observacion_' + entidad"
                        rows="4"
                        class="form-control form-control-sm no-resize"
                        name="observacion"
                      ></textarea>
                    </div>
                  </div>
                </div>

                <div class="row">
                  <div class="col-md-5 col-xs-12">
                    <div class="form-group">
                      <label :for="'flete_' + entidad"
                        >Flete <span class="text-danger">*</span></label
                      >
                      <input
                        type="number"
                        :id="'flete_' + entidad"
                        class="form-control form-control-sm text-center"
                        name="flete"
                        :disabled="campoBloqueado"
                        @change="calcularPreciosVenta()"
                        @keyup.enter="calcularPreciosVenta()"
                        step="0.01"
                        min="0.0"
                        value="0"
                      />
                    </div>
                  </div>

                  <div class="col-md-5 col-xs-12">
                    <label :for="'tipoMoneda_' + entidad"
                      >Moneda <span class="text-danger">*</span></label
                    >
                    <select
                      class="custom-select custom-select-sm"
                      :disabled="detalles.length > 0"
                      @change="capturarMoneda"
                      name="tipoMoneda"
                      :id="'tipoMoneda_' + entidad"
                    >
                      <!-- <option value="" disabled="" selected="">Seleccione</option> -->
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
              </div>

              <div class="col-md-8 col-xs-12">
                <div class="row">
                  <div class="col-md-5 col-xs-12 ml-2">
                    <div class="form-group">
                      <label :for="'descripcion_' + entidad"
                        >Producto <span class="text-danger">*</span></label
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

                  <div class="col-md-2 col-xs-12">
                    <div class="form-group">
                      <label :for="'cantidad_' + entidad"
                        >Cantidad <span class="text-danger">*</span></label
                      >
                      <input
                        type="number"
                        :id="'cantidad_' + entidad"
                        class="form-control form-control-sm text-right"
                        name="cantidad"
                        min="0.01"
                        value="1"
                        step="0.01"
                        @keyup.enter="agregarDetalle"
                      />
                    </div>
                  </div>

                  <div class="col-md-2 col-xs-12">
                    <div class="content-buttons btn-group my-md-4 pt-1">
                      <button
                        @click="agregarDetalle"
                        title="Agregar Detalle"
                        class="btn btn-sm btn-danger"
                        type="button"
                      >
                        <i class="mdi mdi-plus icon-size"></i>
                      </button>
                    </div>
                  </div>
                </div>

                <div class="row">
                  <div class="col-md-11">
                    <div
                      class="table-responsive px-1"
                      style="width:98%;height:200px;margin 2rem auto;"
                      id="tabla-compra"
                    >
                      <table
                        class="table table-bordered table-sm mb-0"
                        id="tabla"
                        v-if="!deshabilitado"
                      >
                        <thead style="background: #275ee5; color: #fff">
                          <tr>
                            <th class="text-center">Tipo</th>
                            <th class="text-center">Descripción</th>
                          </tr>
                        </thead>
                        <tbody>
                          <tr v-if="!arrProducto.length">
                            <td class="text-left text-danger" colspan="2">
                              <strong
                                >No se Encontraron Resultados en su
                                Búsqueda</strong
                              >
                            </td>
                          </tr>

                          <tr v-for="(p, index) in arrProducto" :key="index">
                            <td class="text-center" @click="capturarDato(p)">
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
                                      ? ', Tipo de Llanta: ' + p.tipollanta
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
                      <table
                        class="table table-bordered table-sm mb-0"
                        id="tabla"
                        v-else
                      >
                        <thead style="background: #275ee5; color: #fff">
                          <tr>
                            <th
                              class="text-center"
                              style="vertical-align: middle"
                              rowspan="2"
                            >
                              Tipo
                            </th>
                            <th
                              class="text-center"
                              style="vertical-align: middle"
                              rowspan="2"
                            >
                              Descripción
                            </th>
                            <th class="text-center" colspan="2">Precio</th>
                          </tr>
                          <tr>
                            <th class="text-center">S/</th>
                            <th class="text-center">$</th>
                          </tr>
                        </thead>
                        <tbody>
                          <tr v-if="!arrProducto.length">
                            <td
                              class="text-left text-danger"
                              colspan="4"
                              rowspan="2"
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
                            :class="
                              p.stock == 0 && p.tipo != 'Servicio'
                                ? 'table-danger'
                                : ''
                            "
                          >
                            <td class="text-center" @click="capturarDato(p)">
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
                                      ? ', Tipo de Llanta: ' + p.tipollanta
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
                            <td
                              class="text-right"
                              style="width: 30px"
                              v-text="p.precioS"
                              @click="capturarDato(p)"
                            ></td>
                            <td
                              class="text-right"
                              style="width: 30px"
                              v-text="p.precioD"
                              @click="capturarDato(p)"
                            ></td>
                            <!--<td class="text-right" style="width:30px;" v-text="p.tiempo"></td>-->
                          </tr>
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>

                <div class="row">
                  <div class="col-md-12" :id="'data-stock_' + entidad"></div>
                </div>

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
              </div>
            </div>

            <div class="row">
              <div
                class="table-responsive px-1"
                id="tabla-compra-crear"
                style="width:98%;height:350px;margin 2rem auto;"
              >
                <table class="table mb-0" v-if="!deshabilitado">
                  <thead class="table-info">
                    <tr>
                      <th class="text-left" style="width: 20px">#</th>
                      <th class="text-center" style="width: 60px">Cantidad</th>
                      <th class="text-center" style="width: 280px">Producto</th>
                      <th class="text-center" style="width: 50px">P. Compra</th>
                      <th class="text-center" style="width: 50px">P. Venta</th>
                      <th class="text-center" style="width: 50px">Sub Total</th>
                      <th class="text-center" style="width: 30px"></th>
                    </tr>
                  </thead>
                  <tbody style="height: 250px">
                    <tr v-for="(p, index) in detalles" :key="index">
                      <td
                        style="width: 20px"
                        class="text-left"
                        v-text="index + 1 < 10 ? '0' + (index + 1) : index + 1"
                      ></td>
                      <td style="width: 60px" class="text-center px-1">
                        <input
                          type="number"
                          v-focus-on-create
                          step="0.01"
                          class="form-control form-control-sm text-center"
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
                          class="form-control form-control-sm no-rezise"
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
                      <td style="width: 50px" class="text-center px-1">
                        <input
                          type="number"
                          step="0.01"
                          class="form-control form-control-sm text-center"
                          :name="'txtprecio' + p.id"
                          min="0"
                          :id="'txtprecio' + p.id"
                          placeholder="P. Compra"
                          @keyup.enter="calcularTotalItem(p.id)"
                          @change="calcularTotalItem(p.id)"
                        />
                      </td>
                      <td style="width: 50px" class="text-center px-1">
                        <input
                          type="number"
                          step="0.01"
                          class="form-control form-control-sm text-center"
                          :name="'txtprecioventa' + p.id"
                          min="0"
                          :id="'txtprecioventa' + p.id"
                          :value="p.precioventa"
                          placeholder="P. Venta"
                          readonly=""
                        />
                      </td>
                      <td style="width: 50px" class="text-center px-1">
                        <input
                          type="text"
                          class="form-control form-control-sm text-center"
                          :name="'txtsubtototal' + p.id"
                          :id="'txtsubtototal' + p.id"
                          :value="Math.round(p.precio * p.cantidad * 100) / 100"
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
                          @click="eliminarC(p.id, p.tipo)"
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
                        <span class="text-right" v-text="subtotal"></span
                        ><input
                          type="hidden"
                          name="subtotalDoc"
                          :value="subtotal"
                        />
                      </th>
                      <th>
                        IGV: <span v-text="tipo"></span>
                        <span class="text-right" v-text="igv"></span
                        ><input type="hidden" name="igvDoc" :value="igv" />
                      </th>
                      <th>
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
                <table class="table mb-0" v-else>
                  <thead class="table-info">
                    <tr>
                      <th class="text-left" style="width: 20px">#</th>
                      <th class="text-center" style="width: 60px">Cantidad</th>
                      <th class="text-center" style="width: 280px">Producto</th>
                      <th class="text-center" style="width: 50px">Precio</th>
                      <th class="text-center" style="width: 50px">Sub Total</th>
                      <th class="text-center" style="width: 30px"></th>
                    </tr>
                  </thead>
                  <tbody style="height: 250px">
                    <tr v-for="(p, index) in detalles" :key="index">
                      <td
                        style="width: 20px"
                        class="text-left"
                        v-text="index + 1 < 10 ? '0' + (index + 1) : index + 1"
                      ></td>
                      <td style="width: 60px" class="text-center px-1">
                        <input
                          type="number"
                          v-focus-on-create
                          step="0.01"
                          class="form-control form-control-sm text-center"
                          :name="'txtcantidad' + p.id"
                          :id="'txtcantidad' + p.id"
                          :value="p.cantidad"
                          min="0.01"
                          :max="p.stock"
                          @keyup.enter="calcularTotalItem(p.id)"
                          @change="calcularTotalItem(p.id)"
                        />
                      </td>
                      <td style="width: 280px" class="text-center">
                        <textarea
                          type="text"
                          style="resize: none"
                          class="form-control form-control-sm no-rezise"
                          :name="'txtproducto' + p.id"
                          :id="'txtproducto' + p.id"
                          v-text="p.descripcion"
                          @change="calcularTotalItem(p.id)"
                        ></textarea>
                        <input
                          type="hidden"
                          :name="'productoid' + p.id"
                          :id="'productoid' + p.id"
                          :value="p.idP"
                        />
                        <input
                          type="hidden"
                          :name="'tipo' + p.id"
                          :id="'tipo' + p.id"
                          :value="p.tipo"
                        />
                      </td>
                      <td style="width: 50px" class="text-center px-1">
                        <input
                          type="number"
                          step="0.01"
                          class="form-control form-control-sm text-center"
                          :name="'txtprecio' + p.id"
                          min="0"
                          :id="'txtprecio' + p.id"
                          :value="p.precio"
                          @keyup.enter="calcularTotalItem(p.id)"
                          @change="calcularTotalItem(p.id)"
                        />

                        <input
                          type="hidden"
                          :name="'stock' + p.id"
                          :id="'stock' + p.id"
                          :value="p.stock"
                        />
                        <input
                          type="hidden"
                          :name="'lote' + p.id"
                          :id="'lote' + p.id"
                          :value="p.lote"
                        />
                      </td>

                      <td style="width: 50px" class="text-center px-1">
                        <input
                          type="text"
                          class="form-control form-control-sm text-right"
                          :name="'txtsubtototal' + p.id"
                          :id="'txtsubtototal' + p.id"
                          :value="Math.round(p.precio * p.cantidad * 100) / 100"
                          readonly=""
                        />
                      </td>

                      <td style="width: 30px">
                        <a
                          href="javascript:void(0)"
                          class="btn-danger btn-sm"
                          @click="eliminarV(p.idP, p.id, p.tipo)"
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
                        <span class="text-right" v-text="subtotal"></span
                        ><input
                          type="hidden"
                          name="subtotalDoc"
                          :value="subtotal"
                        />
                      </th>
                      <th>
                        IGV: <span v-text="tipo"></span>
                        <span class="text-right" v-text="igv"></span
                        ><input type="hidden" name="igvDoc" :value="igv" />
                      </th>
                      <th>
                        TOTAL: <span v-text="tipo"></span>
                        <span class="text-right" v-text="total"></span
                        ><input type="hidden" name="totalDoc" :value="total" />
                      </th>
                    </tr>
                  </tfoot>
                </table>
              </div>
            </div>

            <div class="row">
              <div class="col-md-12" :id="'data-error-modal_' + entidad"></div>
            </div>
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
              @click="enviarFormModalAct('modalInventario', entidad, 'MOV_A')"
              :id="'btnEnvio_' + entidad"
              class="btn btn-success btn-sm"
            >
              <i class="mdi mdi-check-bold icon-size"></i> Guardar
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
  name: "DetalleInventario",
  mixins: [misMixins],
  data() {
    return {
      tipoMovimiento: [
        { id: "E", descripcion: "Entradas" },
        { id: "S", descripcion: "Salidas" },
      ],
      listProductos: [],
      listServicios: [],
      listAutos: [],
      idalmacen: this.$attrs.idalmacen,
      lugar: this.$attrs.lugar,
      arrTipos: [],
      arrProducto: [],
      token: this.$store.state.token,
      detalles: [],
      total: 0,
      subtotal: 0,
      igv: 0,
      listDetalles: [],
      stockMax: 1,
      ultElemento: "",
      producto: "",
      tipo: "S/ ",
      campoBloqueado: false,
      deshabilitado: false,
      elementoSeleccionado: {},
      min: 1,
      max: 10000,
      tiposCambio: [
        { id: "S", nombre: "Soles" },
        { id: "D", nombre: "Dólares" },
      ],
      entidad: "mov_inventario",
    };
  },
  methods: {
    capturarMoneda() {
      let me = this;
      let moneda = document.getElementById("tipoMoneda_" + me.entidad).value;
      if (moneda == "S") {
        me.tipo = moneda + "/";
      } else {
        me.tipo = "$";
      }
      me.calcularPreciosVenta();
    },
    cerrarModal () {
      // $('.fade').remove();
      // $('body').removeClass('modal-open');
      // $('.modal-backdrop').remove();

      $('#modalInventario').modal('toggle')
      $('#modalInventario').css('z-index', '-1')
      $('#header-sec').css('z-index','')
      $('#aside-sec').css('z-index','')         
    },
    capturarTipoOperacion() {
      let me = this;
      let valor = document.getElementById(
        "select_tipo_operacion_" + me.entidad
      ).value;
      me.deshabilitado = true;
      if (valor == "E") {
        me.deshabilitado = false;
      } else {
        document.getElementById("flete_" + me.entidad).value = 0;
      }
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

      document.getElementById(`data-stock_${me.entidad}`).innerHTML = "";
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
    capturarDato(ref) {
      let me = this;
      let tipoMovimiento = document.getElementById(
        "select_tipo_operacion_" + me.entidad
      ).value;
      let element = document.getElementById("cantidad_" + me.entidad);
      // alert(ref.nombre)
      let id_ref = ref.id;
      if (tipoMovimiento == "S") {
        id_ref = `${ref.id}@${ref.lote_id}`;
      }
      let rpta = me.buscarInArray(id_ref, ref.tipo);
      document.getElementById("data-stock_" + me.entidad).innerHTML = "";
      // console.log('ref', ref)
      me.elementoSeleccionado = ref;
      if (!rpta) {
        document.getElementById("data-stock_" + me.entidad).innerHTML =
          '<div style = "margin-top:10px;"><div class="alert alert-warning text-left alert-dismissible" role="alert"><button type = "button" class="close" data-dismiss = "alert">&times;</button><strong>Producto ya antes Agregado</strong></a></div ></div>';
      }

      // if (tipo == 'S') {
      document.getElementById("data-stock_" + me.entidad).innerHTML =
        '<div style = "margin-top:10px;"><div class="alert alert-success text-left alert-dismissible" role="alert"><button type = "button" class="close" data-dismiss = "alert">&times;</button><strong>Stock Disponible: ' +
        ref.stock +
        "</strong></a></div ></div>";
      // }
      element.focus();
    },
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
    async showModal(attr) {
      let me = this;
      document.getElementById("data-error-modal_" + me.entidad).innerHTML = "";
      $("#modalInventario").css("z-index", "-1");
      document.getElementById("fecha_" + me.entidad).value =
        me.obtenerFechaActual();
      me.idalmacen = attr;

      if (me.idalmacen > 0) {
        let datos02 = [
          { value: "A", label: "Accesorios/Repuestos" },
          { value: "LL", label: "Llantas" },
          { value: "I", label: "Insumos" },
        ];
        me.arrTipos = datos02;
        me.tipoProducto = "";
        me.producto = "";
        me.listDetalles = [];
        me.arrProducto = [];
        me.listProductos = [];
        me.detalles = [];
        me.total = 0;
        me.subtotal = 0;
        me.igv = 0;
        me.stockMax = 1;
        me.ultElemento = "";
        me.tipoProducto = "";
        me.producto = "";
        me.campoBloqueado = false;
        me.deshabilitado = false;

        document.getElementById("select_tipo_operacion_" + me.entidad).value =
          "";
        document.getElementById("data-stock_" + me.entidad).innerHTML = "";

        document.getElementById(`observacion_${me.entidad}`).value = "";
        document.getElementById(`flete_${me.entidad}`).value = "0";
        document.getElementById(`descripcion_${me.entidad}`).value = "";
        document.getElementById(`cantidad_${me.entidad}`).value = "1";

        $("#modalInventario").modal({ backdrop: "static", show: true, keyboard: false });
        $("#modalInventario").css("z-index", "1500");
        $(".modal-backdrop").css("z-index", "1");
        $('#header-sec').css('z-index','1')
        $('#aside-sec').css('z-index','1')  
      }
    },
    async seleccionarProducto(item) {
      let me = this;
      me.deshabilitado = true;
      let tipoMovimiento = document.getElementById(
        "select_tipo_operacion_" + me.entidad
      ).value;
      // let arrVal = item.value.split('@')
      // me.stockMax = arrVal[3]

      // document.getElementById('data-stock').innerHTML = '<div style = "margin-top:10px;"><div class="alert alert-success text-left alert-dismissible" role="alert"><button type = "button" class="close" data-dismiss = "alert">&times;</button><strong>Stock Disponible: '+me.stockMax+'</strong></a></div ></div>'

      if (tipoMovimiento == "E") {
        me.deshabilitado = false;
      } else {
      }

      window.setTimeout(function () {
        document.getElementById("cantidad_" + me.entidad).focus();
      }, 500);
    },
    /*calcularTotalItem (id) {
            let me = this
            let cantidad = document.getElementById('txtcantidad'+id).value
            let precio  = document.getElementById('txtprecio'+id).value
            let descripcion =  document.getElementById('txtproducto'+id).value
            let stock = document.getElementById('stock'+id).value
            let tipoMovimiento = document.getElementById('select_tipo_operacion').value
    
            document.getElementById('data-stock').innerHTML = ''
            if (tipoMovimiento == 'S') {
                if (cantidad <= parseInt(stock)) {
                    me.detalles.forEach(element => {
                        if (element.id == id) {
                            element.cantidad = cantidad
                            element.precio   = precio
                            element.descripcion = descripcion
                        }
                    })
                    // console.log(me.detalles)
                    me.calcularTotal()
                } else {
                    document.getElementById('data-stock').innerHTML = '<div style = "margin-top:10px;"><div class="alert alert-danger text-left alert-dismissible" role="alert"><button type = "button" class="close" data-dismiss = "alert">&times;</button><strong>Cantidad Indicada No Disponible</strong></a></div ></div>'
                }
            } else {
                me.detalles.forEach(element => {
                    if (element.id == id) {
                        element.cantidad = cantidad
                        element.precio   = precio
                        element.descripcion = descripcion
                    }
                })
                // console.log(me.detalles)
                me.calcularTotal()
            }
            // document.getElementById('txtsubtototal'+id).value = ((cantidad*precio)*1000)/1000
            // me.calcularTotal()
        },*/
    calcularTotalItem(id) {
      let me = this;
      let tipo = document.getElementById(
        "select_tipo_operacion_" + me.entidad
      ).value;
      if (tipo == "E") {
        let cantidad = document.getElementById("txtcantidad" + id).value;
        let precio = document.getElementById("txtprecio" + id).value;
        let descripcion = document.getElementById("txtproducto" + id).value;
        // let stock = document.getElementById('stock'+id).value

        document.getElementById(`data-stock_${me.entidad}`).innerHTML = "";
        // if (cantidad <= parseInt(stock)) {
        me.detalles.forEach((element) => {
          if (element.id == id) {
            element.cantidad = cantidad;
            element.precio = precio;
            element.descripcion = descripcion;

            me.calcularPrecioVenta(id);
          }
        });
        // me.calcularPrecioVenta(id)
        // console.log(me.detalles)
        // me.calcularPreciosVenta()
      } else {
        let cantidad = document.getElementById("txtcantidad" + id).value;
        let precio = document.getElementById("txtprecio" + id).value;
        let descripcion = document.getElementById("txtproducto" + id).value;
        let stock = document.getElementById("stock" + id).value;

        document.getElementById(`data-stock_${me.entidad}`).innerHTML = "";
        if (cantidad > parseFloat(stock)) {
          document.getElementById(`data-stock_${me.entidad}`).innerHTML =
            '<div style = "margin-top:10px;"><div class="alert alert-warning text-left alert-dismissible" role="alert"><button type = "button" class="close" data-dismiss = "alert">&times;</button><strong>Cantidad Indicada No Disponible</strong></a></div ></div>';
        }

        me.detalles.forEach((element) => {
          if (element.id == id) {
            element.cantidad = cantidad;
            element.precio = precio;
            element.descripcion = descripcion;
          }
        });
        // console.log(me.detalles)
      }
      me.calcularTotal();

      // } else {
      //     document.getElementById('data-stock').innerHTML = '<div style = "margin-top:10px;"><div class="alert alert-danger text-left alert-dismissible" role="alert"><button type = "button" class="close" data-dismiss = "alert">&times;</button><strong>Cantidad Indicada No Disponible</strong></a></div ></div>'
      // }
      // document.getElementById('txtsubtototal'+id).value = ((cantidad*precio)*1000)/1000
      // me.calcularTotal()
    },
    async cargarProductos() {
      let me = this;
      let tipo = document.getElementById(
        "select_tipo_operacion_" + me.entidad
      ).value;
      let descripcion = document.getElementById(
        "descripcion_" + me.entidad
      ).value;

      if (tipo != "") {
        let url = "/obtenerproductosmov";
        if (tipo == "E") {
          url = "/obtenerproductoscompra";
        }

        let productos = await axios({
          method: "post",
          url: url,
          data: {
            descripcion: descripcion,
            _token: this.$store.state.token,
          },
        });
        me.arrProducto = productos.data.productos;
      } else {
        me.arrProducto = [];
      }
    },
    eliminarV(idP, id, tipo) {
      // alert(idP + '-'+id + '--'+tipo)
      let me = this;
      // alert(tipo)
      if (tipo == "Producto") {
        for (let j = 0; j < me.listProductos.length; j++) {
          let el = me.listProductos[j];
          if (el == idP) {
            me.listProductos.splice(j, 1);
          }
        }
      }

      for (let i = 0; i < me.detalles.length; i++) {
        let element = me.detalles[i];
        if (element.id == id) {
          if (element.tipo == tipo) {
            me.detalles.splice(i, 1);
            me.listDetalles.splice(i, 1);
          }
        }
      }

      me.campoBloqueado = false;
      if (me.listProductos.length > 0) {
        me.campoBloqueado = true;
      } else {
        me.arrProducto = [];
      }
      me.calcularTotal();
    },
    eliminarC(id, tipo) {
      let me = this;
      // alert(tipo)
      if (tipo == "Producto") {
        for (let j = 0; j < me.listProductos.length; j++) {
          let el = me.listProductos[j];
          if (el == id) {
            me.listProductos.splice(j, 1);
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

      me.campoBloqueado = false;
      if (me.listProductos.length > 0) {
        me.campoBloqueado = true;
      } else {
        me.arrProducto = [];
      }
      me.calcularPreciosVenta();
      me.calcularTotal();
    },
    calcularTotal() {
      let me = this;
      let acum = 0;
      me.detalles.forEach((element) => {
        acum += element.cantidad * element.precio;
      });
      me.total = (parseFloat(acum) * 1000) / 1000;
      me.total = Math.round(me.total * 100) / 100;
      me.igv = 0;
      me.subtotal = me.total;
    },
    agregarDetalle() {
      let me = this;
      let ref = me.elementoSeleccionado;
      let tipoMovimiento = document.getElementById(
        "select_tipo_operacion_" + me.entidad
      ).value;
      let id_ref = ref.id;
      if (tipoMovimiento == "S") {
        id_ref = `${ref.id}@${ref.lote_id}`;
      }

      let rpta = me.buscarInArray(id_ref, ref.tipo);
      let cantidad = document.getElementById("cantidad_" + me.entidad).value;
      document.getElementById("data-stock_" + me.entidad).innerHTML = "";
      let band = false;
      // document.getElementById('data-stock').innerHTML = ''

      if (tipoMovimiento != "") {
        if (rpta) {
          if (tipoMovimiento == "E") {
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
            me.deshabilitado = false;
          } else {
            if (cantidad > parseFloat(ref.stock)) {
              document.getElementById("data-stock_" + me.entidad).innerHTML =
                '<div style = "margin-top:10px;"><div class="alert alert-warning text-left alert-dismissible" role="alert"><button type = "button" class="close" data-dismiss = "alert">&times;</button><strong>Cantidad Indicada No Disponible</strong></a></div ></div>';
            }

            let descripcion =
              ref.nombre +
              (ref.medida != "-" ? ", Medida: " + ref.medida : "") +
              (ref.tipollanta != "-"
                ? ", Tipo de Llanta: " + ref.tipollanta
                : "") +
              (ref.modelo != "-" ? ", Modelo: " + ref.modelo : "") +
              (ref.sistema != "-" ? " , Sistema: " + ref.sistema : "");
            let moneda = document.getElementById(
              `tipoMoneda_${me.entidad}`
            ).value;
            let monto = ref.precioSoles;
            if (moneda == "D") {
              monto = ref.precioDolares;
            }

            if (descripcion == "") {
              descripcion = ref.nombre2;
            }
            var elemento = {
              id: me.generarCorrelativo(),
              tipo: ref.tipo,
              precio: monto,
              descripcion: descripcion,
              stock: ref.stock,
              cantidad: cantidad,
              stockMax: me.stockMax,
              tiempo: ref.tiempo,
              idP: ref.id,
              lote: ref.lote_id,
            };

            if (ref.tipo == "Producto") {
              let id_push = `${ref.id}@${ref.lote_id}`;
              me.listProductos.push(id_push);
              band = true;
            }
            me.deshabilitado = true;
          }

          if (band) {
            me.detalles.push(elemento);
            if (tipoMovimiento == "S") {
              me.listDetalles.push(elemento.id);
            }
            me.ultElemento = ref.id;
            me.producto = "";
            document.getElementById("cantidad_" + me.entidad).value = 1;
            me.calcularTotal();
            // me.deshabilitado = false
            me.campoBloqueado = true;
          }
        } else {
          document.getElementById("data-stock_" + me.entidad).innerHTML =
            '<div style = "margin-top:10px;"><div class="alert alert-warning text-left alert-dismissible" role="alert"><button type = "button" class="close" data-dismiss = "alert">&times;</button><strong>Producto ya antes Agregado</strong></a></div ></div>';
        }
      } else {
        document.getElementById("data-stock_" + me.entidad).innerHTML =
          '<div style = "margin-top:10px;"><div class="alert alert-warning text-left alert-dismissible" role="alert"><button type = "button" class="close" data-dismiss = "alert">&times;</button><strong>Seleccione Tipo de Operación</strong></a></div ></div>';
      }
      // console.log(me.listDetalles)
    },
    generarCorrelativo() {
      let me = this;
      let numero = me.getRandomNumber(me.min, me.max);
      let idx = me.detalles.indexOf(numero);
      do {
        if (idx != -1) {
          numero = me.getRandomNumber(me.min, me.max);
          idx = me.detalles.indexOf(numero);
        }
      } while (idx != -1);

      return numero;
    },
  },
  created() {},
  activated: function () {
    let me = this;
    this.$store.state.mostrarModal = !this.$store.state.mostrarModal;
    me.arrTipos = [];
    me.arrProducto = [];
    me.detalles = [];
    me.total = 0;
    me.subtotal = 0;
    me.igv = 0;
    me.listDetalles = [];
    me.stockMax = 1;
    me.ultElemento = "";
    me.tipoProducto = "";
    me.producto = "";
    me.campoBloqueado = false;
  },
  deactivated: function () {},
  mounted() {},
  beforeDestroy: function () {
    // let me  = this
    // me.arreglo = []
    // // console.log(me.arreglo)
    // me.arreglo2 = []
    // me.opciones = ''
    // this.$store.state.mostrarModal = !this.$store.state.mostrarModal
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

#tabla-compra {
  cursor: pointer;
}
</style>
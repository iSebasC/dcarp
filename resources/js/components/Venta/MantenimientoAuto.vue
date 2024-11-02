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
                <router-link tag="a" to="/venta">Venta</router-link>
              </li>
              <li class="breadcrumb-item active">
                {{ accion }} Venta de Autos
              </li>
            </ol>
          </div>
        </div>
        <h4 class="content-header-title mb-0 mt-1">
          {{ accion }} Venta de Autos
        </h4>
      </div>
    </div>
    <div class="content-body">
      <!-- Basic form layout section start -->
      <section id="basic-form-layouts">
        <div class="row match-height">
          <div class="col-md-11 col-xs-12">
            <div class="card">
              <!-- <div class="card-header pb-1">
                <h4 class="card-title" id="basic-layout-form">
                  <i class="la la-car"></i> Datos de la Venta
                </h4>
              </div> -->
              <div class="card-content collapse show">
                <div class="card-body">
                  <form
                    class="form"
                    :id="'formEnv_' + entidad"
                    method="POST"
                    action="/guardarventaauto"
                  >
                    <input
                      type="hidden"
                      :value="this.$store.state.token"
                      name="_token"
                    />

                    <div class="form-body">
                      <div class="row">
                        <div class="col-md-6">
                          <div class="row">
                            <div class="col-md-4 col-xs-12">
                              <div class="form-group">
                                <label :for="'fecha_' + entidad"
                                  >Fecha
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
                                  readonly=""
                                />
                              </div>
                            </div>

                            <div class="col-md-6 col-xs-12">
                              <label :for="'select_tipodocumento_' + entidad"
                                >Tipo de Comprobante
                                <span class="text-danger">*</span></label
                              >
                              <select
                                class="custom-select custom-select-sm"
                                name="tipodocumento"
                                :id="'select_tipodocumento_' + entidad"
                                @change="tipoVenta"
                                v-model="tipoComprobante"
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

                          <div class="row">

                            <div class="col-md-4 col-xs-12" v-if="tipoComprobante != 'F'">
                              <label :for="'select_tipopersona_' + entidad"
                                >T. Documento
                                <span class="text-danger">*</span></label
                              >
                              <select
                                class="custom-select custom-select-sm"
                                name="tipopersona"
                                :id="'select_tipopersona_' + entidad"
                                v-model="tipoPersona"
                                @change="setTipoPersona()"
                              >
                                <!-- <option value="" disabled="" selected="">
                                  Seleccione
                                </option> -->
                                <option
                                  v-for="f in tiposPersona"
                                  :key="f.id"
                                  :value="f.id"
                                >
                                  {{ f.nombre }}
                                </option>
                              </select>
                            </div>
                            <div class="col-md-4 col-xs-12">
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
                                  maxlength="8"
                                  minlength="8"
                                  @keypress="soloNumeros($event)"
                                  @keyup.enter="busquedaCliente"
                                />
                              </div>
                            </div>

                            <div class="col-md-2 col-xs-12">
                              <div class="content-buttons btn-group my-md-3 pt-2">
                                <button
                                  @click="busquedaCliente"
                                  title="Buscar Cliente"
                                  class="btn btn-sm btn-info"
                                  type="button"
                                  style="margin-top: 5px"
                                >
                                  <i class="mdi mdi-magnify icon-size"></i>
                                </button>
                                <button
                                  v-if="bandCliente02"
                                  @click="verCliente"
                                  title="Ver Cliente"
                                  class="btn btn-sm btn-warning"
                                  type="button"
                                  style="margin-top: 5px"
                                >
                                  <i class="mdi mdi-file-eye icon-size"></i>
                                </button>
                                <button
                                  v-if="bandCliente"
                                  @click="agregarCliente"
                                  title="Buscar Cliente"
                                  class="btn btn-sm btn-danger"
                                  type="button"
                                  style="margin-top: 5px"
                                >
                                  <i class="mdi mdi-plus icon-size"></i>
                                </button>
                              </div>
                            </div>
                            <!-- <div class="col-md-6 col-xs-12">
                                                            <div class="form-group">
                                                                <label for="bq">Buscar Por <span class="text-danger">*</span> 
                                                                </label>
                                                                <div id="bq">
                                                                    <input class="mr-1 mt-1 chk" type="radio" checked="" 
                                                                    @change="seleccionarTipoVenta('A')" :disabled="campoBloqueado"
                                                                    name="chkTipoVenta">Producto/Servicio
                                                                    <br>
                                                                    <input class="mr-1 mt-1 chk" type="radio" 
                                                                    @change="seleccionarTipoVenta('C')" :disabled="campoBloqueado"
                                                                    name="chkTipoVenta">Cotización
                                                                    <br>
                                                                    <input class="mr-1 mt-1 chk" type="radio" 
                                                                    @change="seleccionarTipoVenta('O')" :disabled="campoBloqueado"
                                                                    name="chkTipoVenta">Orden de Trabajo
                                                                </div>
                                                            </div>
                                                        </div> -->
                          </div>
                          <div class="row">
                            <div class="col-md-10 col-xs-12">
                              <div class="form-group">
                                <label :for="'cliente_' + entidad"
                                  >Cliente
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
                            <div
                              class="col-md-12"
                              :id="'data-cliente_' + entidad"
                            ></div>
                          </div>

                          <div class="row">
                            <div class="col-md-5 col-xs-12">
                              <label :for="'select_serie_' + entidad"
                                >Serie <span class="text-danger">*</span></label
                              >
                              <select
                                class="custom-select custom-select-sm"
                                :id="'select_serie_' + entidad"
                                name="serie"
                                :disabled="bloqueo_serie_combo"
                              >
                                <!-- <option value="" disabled="" selected="">Seleccione</option> -->
                                <option
                                  v-for="f in series"
                                  :key="f.id"
                                  :value="f.id"
                                >
                                  {{ f.serie }}
                                </option>
                              </select>
                            </div>
                            <div class="col-md-5 col-xs-12">
                              <label :for="'select_tipo_operacion_' + entidad"
                                >Método de Pago
                                <span class="text-danger">*</span></label
                              >
                              <select
                                class="custom-select custom-select-sm"
                                :disabled="tipoComprobante != 'F'"
                                :id="'select_tipo_operacion_' + entidad"
                                v-model="metodoPago"
                                name="tipopago"
                              >
                                <!-- <option value="" disabled="" selected="">Seleccione</option> -->
                                <option
                                  v-for="f in tipoOperacion"
                                  :key="f.id"
                                  :value="f.id"
                                >
                                  {{ f.nombre }}
                                </option>
                              </select>
                            </div>
                          </div>
                          <div class="row mt-2">
                            <div
                              class="col-md-3 col-xs-12"
                              v-if="metodoPago == 'D' && tipoComprobante == 'F'"
                            >
                              <div class="form-group">
                                <label :for="'nrodias_' + entidad"
                                  >Nro Días
                                  <span class="text-danger">*</span></label
                                >
                                <input
                                  type="text"
                                  :id="'nrodias_' + entidad"
                                  maxlength="2"
                                  @keypress="soloNumeros($event)"
                                  v-model="nroDias"
                                  name="nrodias"
                                  class="form-control form-control-sm"
                                />
                              </div>
                            </div>
                            <div class="col-md-4 col-xs-12">
                              <label :for="'select_forma_pago_' + entidad"
                                >Forma de Pago
                                <span class="text-danger">*</span></label
                              >
                              <select
                                class="custom-select custom-select-sm"
                                :id="'select_forma_pago_' + entidad"
                                name="formapago"
                              >
                                <!-- <option value="" disabled="" selected="">Seleccione</option> -->
                                <option
                                  v-for="f in tipoPago"
                                  :key="f.id"
                                  :value="f.id"
                                >
                                  {{ f.nombre }}
                                </option>
                              </select>
                            </div>
                            <div class="col-md-4 col-xs-12">
                              <label :for="'select_igv_' + entidad"
                                >Tipo Venta
                                <span class="text-danger">*</span></label
                              >
                              <select
                                class="custom-select custom-select-sm"
                                :id="'select_igv_' + entidad"
                                @change="validIgv()"
                                v-model="afectoIgv"
                                name="tipoIgvVenta"
                              >
                                <!-- <option value="" disabled="" selected="">Seleccione</option> -->
                                <option
                                  v-for="f in tipoIgvVenta"
                                  :key="f.id"
                                  :value="f.id"
                                >
                                  {{ f.nombre }}
                                </option>
                              </select>
                            </div>

                            <!-- <div class="col-md-5 col-xs-12">
                                                            <div class="form-group">
                                                                <label :for="'fechavencimiento_'+entidad">Fecha Vencimiento <span class="text-danger">*</span></label> 
                                                                <input type="date" :min="obtenerFechaActual()" :id="'fechavencimiento_'+entidad" name="fechavencimiento" class="form-control form-control-sm text-center">
                                                            </div>
                                                        </div> -->
                          </div>
                          <div class="row mt-2">
                            <div class="col-md-5 col-xs-12">
                              <label :for="'select_moneda_' + entidad"
                                >Moneda
                                <span class="text-danger">*</span></label
                              >
                              <select
                                class="custom-select custom-select-sm"
                                :id="'select_moneda_' + entidad"
                                :disabled="detalles.length > 0"
                                v-model="moneda_s"
                                name="monedaVenta"
                              >
                                <!-- <option value="" disabled="" selected="">Seleccione</option> -->
                                <option
                                  v-for="f in monedas"
                                  :key="f.id"
                                  :value="f.id"
                                >
                                  {{ f.nombre }}
                                </option>
                              </select>
                            </div>

                            <div class="col-md-5 col-xs-12">
                              <label :for="'observacion_' + entidad"
                                >Observaciones
                                <small class="small text-muted"
                                  >(Opcional)</small
                                ></label
                              >
                              <textarea
                                name="observaciones"
                                class="form-control form-control-sm no-resize"
                                :id="'observacion_' + entidad"
                                rows="2"
                              ></textarea>
                            </div>
                          </div>

                          <div class="row mt-1">
                            <div class="col-md-5 col-xs-12">
                              <input
                                class="mr_p-0"
                                type="checkbox"
                                name="chkcaja"
                                value="true"
                                :id="'chk_caja_' + entidad"
                                :checked="chkCaja"
                              />
                              <label
                                class="text-muted"
                                :for="'chk_caja_' + entidad"
                                >Enviar a caja maestra</label
                              >
                            </div>
                          </div>

                          <div class="row mt-1">
                            <div class="col-md-6 col-xs-12">
                              <div class="form-group">
                                <label :for="'asesor_' + entidad"
                                  >Vendedor <span class="text-danger">*</span>
                                  <!-- <small class="small text-muted"
                                    >(Opcional)</small
                                  > -->
                                </label>
                                <select
                                  :id="'asesor_' + entidad"
                                  class="custom-select custom-select-sm"
                                  name="asesor"
                                >
                                  <option value="" disabled="" selected="">
                                    Seleccione
                                  </option>
                                  <option
                                    v-for="(item, index) in trabajadores"
                                    :key="index"
                                    :value="item.personal"
                                  >
                                    {{ item.personal }}
                                  </option>
                                </select>
                                <!-- <input
                                  type="hidden"
                                  :id="'asesor_' + entidad"
                                  maxlength="255"
                                  @keypress="soloLetras($event)"
                                  name="asesor"
                                  class="form-control form-control-sm"
                                  v-model="trabajadorText"
                                /> -->
                                <!-- <vue-typeahead-bootstrap
                                  id="asesor"
                                  :ieCloseFix="false"
                                  inputClass="form-control-sm"
                                  v-model="queryT"
                                  :data="trabajadores"
                                  :serializer="(s) => s.personal"
                                  @hit="selectTrabajador = $event"
                                  @input="getTrabajadores"
                                  @keyup.delete="eliminarTrabajador"
                                  placeholder="Indique Asesor..."
                                /> -->
                              </div>
                            </div>
                          </div>
                        </div>

                        <div
                          v-if="tipoVentaV == 'A'"
                          class="col-md-6 col-xs-12"
                          :id="'sec_producto_' + entidad"
                        >
                          <div class="row">
                            <div class="col-md-6 col-xs-12">
                              <div class="form-group">
                                <label :for="'descripcion_' + entidad"
                                  >Auto
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
                                    text-center
                                  "
                                  name="cantidad"
                                  min="0.01"
                                  step="0.01"
                                  value="1"
                                  @keyup.enter="agregarDetalle"
                                />
                              </div>
                            </div>

                            <div class="col-md-2 col-xs-12">
                              <div class="content-buttons btn-group" style="margin-top:1.8rem;">
                                <button
                                  @click="cargarProductos"
                                  title="Buscar Producto"
                                  class="btn btn-sm btn-info"
                                  type="button"
                                  style="margin-right: 3px"
                                >
                                  <i class="mdi mdi-magnify icon-size"></i>
                                </button>

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
                            <div class="col-md-12">
                              <div
                                class="table-responsive"
                                style="
                                  width: 100%;
                                  height: 300px;
                                  padding-left: 5px;
                                  overflow-x: hidden;
                                "
                                id="tabla-cotizacion"
                              >
                                <table
                                  class="table table-bordered table-sm mb-0"
                                  id="tabla"
                                  style="
                                    border: 1px solid #f2f2f2;
                                    overflow: hidden scroll;
                                  "
                                >
                                  <thead
                                    style="background: #275ee5; color: #fff"
                                  >
                                    <tr>
                                      <th
                                        class="text-center"
                                        width="15%"
                                        style="vertical-align: middle"
                                        rowspan="2"
                                      >
                                        Tipo
                                      </th>
                                      <th
                                        class="text-center"
                                        width="45%"
                                        style="vertical-align: middle"
                                        rowspan="2"
                                      >
                                        Descripción
                                      </th>
                                      <th
                                        class="text-center"
                                        width="40%"
                                        colspan="2"
                                      >
                                        Precio
                                      </th>
                                    </tr>
                                    <tr>
                                      <th class="text-center" width="20%">
                                        S/
                                      </th>
                                      <th class="text-center" width="20%">$</th>
                                    </tr>
                                  </thead>
                                  <tbody>
                                    <tr v-if="!arrProducto.length">
                                      <td
                                        class="text-left text-danger"
                                        width="100%"
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
                                      <td
                                        class="text-center"
                                        width="15%"
                                        @click="capturarDato(p)"
                                      >
                                        <strong v-text="p.tipo"></strong>
                                      </td>
                                      <td
                                        class="text-justify"
                                        width="45%"
                                        @click="capturarDato(p)"
                                      >
                                        <p
                                          class="small"
                                          v-text="p.descripcion"
                                        ></p>
                                        <p
                                          class="small"
                                          v-text="p.descripcionadicional"
                                        ></p>
                                      </td>
                                      <td
                                        class="text-center"
                                        width="20%"
                                        v-text="p.precioS"
                                        @click="capturarDato(p)"
                                      ></td>
                                      <td
                                        class="text-center"
                                        width="20%"
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
                          name="listOrdenes"
                          :id="'listOrdenes_' + entidad"
                          :value="listOrdenes.join(',')"
                        />
                        <input
                          type="hidden"
                          name="listCotizaciones"
                          :id="'listCotizaciones_' + entidad"
                          :value="listCotizaciones.join(',')"
                        />

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
                        <input
                          type="hidden"
                          name="tipoVenta"
                          :value="tipoVentaV"
                        />

                        <div
                          class="table-responsive px-1"
                          id="tabla-venta-crear"
                          style="height: 350px; overflow-y: scroll"
                        >
                          <table class="table table-sm mb-0">
                            <thead class="table-info">
                              <tr>
                                <th class="text-left" style="width: 20px">#</th>
                                <th class="text-center" style="width: 60px">
                                  Cantidad
                                </th>
                                <th class="text-center" style="width: 280px">
                                  Producto/Servicio
                                </th>
                                <th class="text-center" style="width: 50px">
                                  Precio
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
                              <tr v-for="(p, index) in detalles" :key="p.id">
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
                                    :max="p.stockMax"
                                    class="
                                      form-control form-control-sm
                                      text-right
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
                                    maxlength="495"
                                    style="resize: none"
                                    class="
                                      form-control form-control-sm
                                      no-resize
                                    "
                                    :name="'txtproducto' + p.id"
                                    :id="'txtproducto' + p.id"
                                    rows="5"
                                    v-text="p.descripcion"
                                    @change="calcularTotalItem(p.id)"
                                  ></textarea>
                                  <input
                                    type="hidden"
                                    class="form-control input-md"
                                    :name="'productoid' + p.id"
                                    :id="'productoid' + p.id"
                                    :value="p.idP"
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
                                      text-right
                                    "
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
                                    :name="'enlace' + p.id"
                                    :id="'enlace' + p.id"
                                    :value="p.idEnlace"
                                  />
                                  <input
                                    type="hidden"
                                    :name="'tipo' + p.id"
                                    :id="'tipo' + p.id"
                                    :value="p.tipo"
                                  />
                                  <input
                                    type="hidden"
                                    :name="'lote' + p.id"
                                    :id="'lote' + p.id"
                                    :value="p.lote"
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
                                </td>

                                <td style="width: 30px">
                                  <a
                                    href="javascript:void(0)"
                                    class="btn-danger btn-sm"
                                    @click="eliminar(p.id)"
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
                                  SUB TOTAL:
                                  <span
                                    v-text="moneda_s == 'PEN' ? 'S/' : '$'"
                                  ></span>
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
                                <th>
                                  IGV:
                                  <span
                                    v-text="moneda_s == 'PEN' ? 'S/' : '$'"
                                  ></span>
                                  <span class="text-right" v-text="igv"></span
                                  ><input
                                    type="hidden"
                                    name="igvDoc"
                                    :value="igv"
                                  />
                                </th>
                                <th>
                                  TOTAL:
                                  <span
                                    v-text="moneda_s == 'PEN' ? 'S/' : '$'"
                                  ></span>
                                  <span class="text-right" v-text="total"></span
                                  ><input
                                    type="hidden"
                                    name="totalDoc"
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
                        @click="enviarFormDetalles(url, entidad, 'VENT')"
                        :id="'btnEnvio_' + entidad"
                        class="btn btn-sm btn-success"
                      >
                        <i class="mdi mdi-check-bold icon-size"></i> Guardar
                      </button>
                      <button
                        type="button"
                        @click="atras(url)"
                        class="btn btn-sm btn-danger mr-1"
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
    <Cliente ref="cliente" :documento="this.documento"></Cliente>
  </div>
</template>
<script>
import axios from "axios";
import { misMixins } from "../../mixins/mixin.js";
import Cliente from "./Cliente";

export default {
  name: "MantenimientoVenta",
  mixins: [misMixins],
  components: {
    Cliente,
  },
  data() {
    return {
      accion: "Registrar",
      url: "/venta",
      tiposPersona: [
        { id: "1", nombre: "DNI" },
        { id: "3", nombre: "C.E" },
      ],
      tipos: [
        { id: "B", nombre: "Boleta" },
        { id: "F", nombre: "Factura" },
      ],
      monedas: [
        { id: "PEN", nombre: "Soles" },
        { id: "USD", nombre: "Dólares" },
      ],
      tipoOperacion: [
        { id: "C", nombre: "Contado" },
        { id: "D", nombre: "Crédito" },
      ],
      tipoPago: [
        { id: "Efectivo", nombre: "Efectivo" },
        { id: "Depósito", nombre: "Depósito" },
        { id: "Tarjeta", nombre: "Tarjeta" },
      ],
      tipoIgvVenta: [
        { id: "20", nombre: "Exonerada" },
        { id: "30", nombre: "Inafecta" },
        { id: "10", nombre: "Afecta" },
      ],
      documento: "",
      tipoPersona: "",
      bandCliente: false,
      bandCliente02: false,
      bandTienda: false,
      clienteSeleccionado: 0,
      detalles: [],
      total: 0,
      subtotal: 0,
      igv: 0,
      series: [],
      listDetalles: [],
      listProductos: [],
      listServicios: [],
      listAutos: [],
      arrTipos: [],
      arrProducto: [],
      arrOrdenes: [],
      arrOrdenesT: [],
      listOrdenes: [],
      listCotizaciones: [],
      tipoProducto: "",
      producto: "",
      stockMax: 1,
      campoBloqueado: false,
      ultElemento: "",
      tipoVentaV: "A",
      entidad: "mant_venta_auto",
      min: 1,
      max: 999999,
      is_igv: true,
      bloqueo_serie_combo: true,
      metodoPago: "C",
      afectoIgv: "20",
      nroDias: "",
      tipoComprobante: "",
      moneda_s: "PEN",
      chkCaja: true,
      trabajadores: [],
      queryT: "",
      selectTrabajador: null,
      trabajadorId: 0,
      trabajadorText: "",
    };
  },
  watch: {
    // queryT() {},
    // selectTrabajador: function () {
    //   //   console.log("Entre---");
    //   let me = this;
    //   if (me.selectTrabajador != null) {
    //     me.trabajadorId = me.selectTrabajador.id;
    //   } else {
    //     me.trabajadorId = 0;
    //     me.trabajadorText = "";
    //   }
    //   //   this.trabajadorId =
    //   //     this.selectTrabajador != null ? this.selectTrabajador.id : 0;
    // },
  },
  methods: {
    async getTrabajadores() {
      //   const res = await fetch(API_URL.replace(":query", query));
      //   const result = await res.json();
      //   this.trabajadores = result.personal;
      //  async buscarPersonas () {
      let me = this;

      let response = await axios({
        method: "POST",
        url: "/trabajadores/asesor",
        data: {},
      });
      me.trabajadores = response.data.personal;
    },
    async capturarCotizacion(id) {
      let me = this;
      me.listCotizaciones.push(id);
      let response = await axios.get(`/getdetallescotizacionventa/${id}`);
      let arreglo = response.data.detalles;

      arreglo.forEach((el) => {
        // console.log('el', el)
        let elemento = null;
        if (el.tipo == "Servicio") {
          elemento = {
            id: me.generarCorrelativo(),
            tipo: el.tipo,
            precio: el.precio,
            descripcion: el.descripcion,
            stock: el.stock,
            cantidad: el.cantidad,
            stockMax: el.stock,
            tiempo: 0,
            idP: el.id,
            idEnlace: el.id_enlace,
            idP: el.id,
          };
          me.listServicios.push(elemento.id);
        } else {
          elemento = {
            id: me.generarCorrelativo(),
            tipo: el.tipo,
            precio: el.precio,
            descripcion: el.descripcion,
            stock: el.stock,
            cantidad: el.cantidad,
            stockMax: el.stock,
            tiempo: 0,
            idP: el.id,
            idEnlace: el.id_enlace,
            idP: el.id,
            lote: el.lote_id,
          };
          let id_ref = `${el.id}@${el.lote_id}`;
          me.listProductos.push(id_ref);
        }

        me.detalles.push(elemento);
        me.listDetalles.push(elemento.id);
        me.ultElemento = elemento.id;
        me.producto = "";
        me.campoBloqueado = true;
      });
      me.calcularTotal();
    },
    async capturarOrden(id) {
      let me = this;
      me.listOrdenes.push(id);
      let response = await axios.get(`/getdetallesordenventa/${id}`);
      let arreglo = response.data.detalles;

      arreglo.forEach((el) => {
        // console.log('el', el)
        let elemento = null;

        if (el.tipo == "Servicio") {
          elemento = {
            id: me.generarCorrelativo(),
            tipo: el.tipo,
            precio: el.precio,
            descripcion: el.descripcion,
            stock: el.stock,
            cantidad: el.cantidad,
            stockMax: el.stock,
            tiempo: 0,
            idP: el.id,
            idEnlace: el.id_enlace,
            idP: el.id,
          };
          me.listServicios.push(elemento.id);
        } else {
          elemento = {
            id: me.generarCorrelativo(),
            tipo: el.tipo,
            precio: el.precio,
            descripcion: el.descripcion,
            stock: el.stock,
            cantidad: el.cantidad,
            stockMax: el.stock,
            tiempo: 0,
            idP: el.id,
            idEnlace: el.id_enlace,
            idP: el.id,
            lote: el.lote_id,
          };
          let id_ref = `${el.id}@${el.lote_id}`;
          me.listProductos.push(id_ref);
        }

        me.detalles.push(elemento);
        me.listDetalles.push(elemento.id);
        me.ultElemento = elemento.id;
        me.producto = "";
        me.campoBloqueado = true;
      });
      me.calcularTotal();
    },
    eliminarTrabajador() {
      this.selectTrabajador = null;
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
      let id_ref = ref.id;
      if (ref.tipo == "Auto") {
        id_ref = `${ref.id}@${ref.lote_id}`;
      }

      let rpta = me.buscarInArray(id_ref, ref.tipo);
      document.getElementById("data-stock_" + me.entidad).innerHTML = "";
      me.stockMax = ref.stock;
      me.elementoSeleccionado = ref;
      if (rpta) {
        if (ref.tipo != "Servicio") {
          document.getElementById("data-stock_" + me.entidad).innerHTML =
            '<div style = "margin-top:10px;"><div class="alert alert-success text-left alert-dismissible" role="alert"><button type = "button" class="close" data-dismiss = "alert">&times;</button><strong>Stock Disponible: ' +
            me.stockMax +
            "</strong></a></div ></div>";
        } else {
          document.getElementById("data-stock_" + me.entidad).innerHTML =
            '<div style = "margin-top:10px;"><div class="alert   alert-success text-left alert-dismissible" role="alert"><button type = "button" class="close" data-dismiss = "alert">&times;</button><strong>Tiempo Estimado: ' +
            ref.tiempo +
            "</strong></a></div ></div>";
        }
      } else {
        document.getElementById("data-stock_" + me.entidad).innerHTML =
          '<div style = "margin-top:10px;"><div class="alert alert-warning text-left alert-dismissible" role="alert"><button type = "button" class="close" data-dismiss = "alert">&times;</button><strong>Auto ya antes Agregado</strong></a></div ></div>';
      }

      element.focus();
    },
    async cargarDatos() {
      let me = this;
      me.accion = "Registrar";
      document.getElementById("documento_" + me.entidad).value = "";
      document.getElementById("cliente_" + me.entidad).value = "";
      document.getElementById("fecha_" + me.entidad).value =
        me.obtenerFechaActual();
      await me.getTrabajadores();
      // document.getElementById('arrLocal').value=''
      //   let locales = await axios.get('/obtenertiendas')
      //   let array = locales.data.locales
      //   let datos = []
      //   array.forEach(element => {
      //     var l =  { value:element.id, label: element.direccion+'- '+element.departamento}
      //     datos.push(l)
      //   })
      //   me.arrLocales = datos

      //   let tipos = await axios.get('/tipos')
      //   let array02 = tipos.data.tipos
      //   let datos02 = []
      //   array02.forEach(element => {
      //     var a =  { value:element.id, label: element.nombre}
      //     datos02.push(a)
      //   })
      //   me.arrTipos = datos02

      //   $('#arrLocal').selectivity()
    },
    // getValor (value) {
    //     let me = this
    //     console.log('Object:', value)
    // me.idLocales.push(value)
    // console.log(value[1].value)
    // alert(key)
    // },
    async busquedaCliente() {
      let me = this;
      let val = document.getElementById(
        "select_tipodocumento_" + me.entidad
      ).value;
      document.getElementById("data-cliente_" + me.entidad).innerHTML = "";
      if (val == "") {
        document.getElementById("data-cliente_" + me.entidad).innerHTML =
          '<div style = "margin-top:10px;"><div class="alert alert-danger text-left alert-dismissible" role="alert"><button type = "button" class="close" data-dismiss = "alert">&times;</button><strong>Seleccione Tipo de Documento Antes de Seguir...</strong></a></div ></div>';
      } else {
        let valorDoc = document.getElementById("documento_" + me.entidad).value;
        if (valorDoc.length == 8 || valorDoc.length == 9 || valorDoc.length == 11) {
          document.getElementById("data-cliente_" + me.entidad).innerHTML =
            '<div style = "margin-top:10px;"><div class="alert alert-warning text-left alert-dismissible" role="alert"><button type = "button" class="close" data-dismiss = "alert">&times;</button><strong>Espere...</strong></a></div ></div>';

          let response = await axios.get(
            "/obtenercliente/" + valorDoc + "/" + val
          );

          window.setTimeout(function () {
            if (response.data.estado) {
              let razonSocial = "";
              if (response.data.cliente.tipoDocumento == "PJ") {
                razonSocial = response.data.cliente.razonSocial;
              } else {
                razonSocial =
                  response.data.cliente.apellidos +
                  " " +
                  response.data.cliente.nombres;
              }
              document.getElementById("cliente_" + me.entidad).value =
                razonSocial;
              document.getElementById("data-cliente_" + me.entidad).innerHTML =
                "";
              me.bandCliente02 = true;
              me.bandCliente = false;
              me.clienteSeleccionado = response.data.cliente.id;
            } else {
              me.bandCliente02 = false;
              me.bandCliente = true;
              me.clienteSeleccionado = 0;
              document.getElementById("cliente_" + me.entidad).value = "";
              document.getElementById("data-cliente_" + me.entidad).innerHTML =
                '<div style = "margin-top:10px;"><div class="alert alert-warning text-left alert-dismissible" role="alert"><button type = "button" class="close" data-dismiss = "alert">&times;</button><strong>Cliente no Registrado, Antes de Seguir Regístrelo.</strong></a></div ></div>';
            }
          }, 500);
        } else {
          document.getElementById("data-cliente_" + me.entidad).innerHTML =
            '<div style = "margin-top:10px;"><div class="alert alert-warning text-left alert-dismissible" role="alert"><button type = "button" class="close" data-dismiss = "alert">&times;</button><strong>El Documento debe ser DNI/CE o RUC</strong></a></div ></div>';
        }
      }
    },
    async cargarProductos() {
      let me = this;
      if (me.tipoVentaV == "A") {
        let descripcion = document.getElementById(
          "descripcion_" + me.entidad
        ).value;
        let productos = await axios({
          method: "post",
          url: "/obtenerproductosautos",
          data: {
            descripcion: descripcion,
            _token: this.$store.state.token,
          },
        });
        me.arrProducto = productos.data.productos;
      }
    },
    async seleccionarTipoVenta(item) {
      let me = this;
      // console.log('item', item)
      me.tipoVentaV = item;
      // me.tipoProducto = item
      // me.cargarProductos()
    },
    async seleccionarProducto(item) {
      let me = this;
      let arrVal = item.value.split("@");
      me.stockMax = arrVal[3];

      document.getElementById("data-stock_" + me.entidad).innerHTML =
        '<div style = "margin-top:10px;"><div class="alert alert-success text-left alert-dismissible" role="alert"><button type = "button" class="close" data-dismiss = "alert">&times;</button><strong>Stock Disponible: ' +
        me.stockMax +
        "</strong></a></div ></div>";

      window.setTimeout(function () {
        document.getElementById("cantidad_" + me.entidad).focus();
      }, 500);
      // document.getElementById('cantidad').focus()
    },
    seleccionarAlmacen(item) {
      let me = this;
      me.almacenSeleccionado = item;
      me.almacenId = item.value;
    },
    calcularTotalItem(id) {
      let me = this;
      let cantidad = document.getElementById("txtcantidad" + id).value;
      let precio = document.getElementById("txtprecio" + id).value;
      let descripcion = document.getElementById("txtproducto" + id).value;
      let stock = document.getElementById("stock" + id).value;

      document.getElementById("data-stock_" + me.entidad).innerHTML = "";
      if (cantidad <= parseFloat(stock)) {
        me.detalles.forEach((element) => {
          if (element.id == id) {
            element.cantidad = cantidad;
            element.precio = precio;
            element.descripcion = descripcion;
          }
        });
        // console.log(me.detalles)
        me.calcularTotal();
      } else {
        document.getElementById("data-stock_" + me.entidad).innerHTML =
          '<div style = "margin-top:10px;"><div class="alert alert-danger text-left alert-dismissible" role="alert"><button type = "button" class="close" data-dismiss = "alert">&times;</button><strong>Cantidad Indicada No Disponible</strong></a></div ></div>';
      }
      // document.getElementById('txtsubtototal'+id).value = ((cantidad*precio)*1000)/1000
      // me.calcularTotal()
    },
    eliminarDetalles(elem) {
      let me = this;
      // console.log('elem', elem)
      if (elem.tipo == "Servicio") {
        let idx = me.listServicios.indexOf(elem.idP);
        if (idx != -1) {
          me.listServicios.splice(idx, 1);
        }
      } else if (elem.tipo == "Auto") {
        let ref = `${elem.idP}@${elem.lote}`;
        let idx = me.listAutos.indexOf(ref);
        if (idx != -1) {
          me.listAutos.splice(idx, 1);
        }
      }
    },
    eliminar(id) {
      let me = this;
      for (let i = 0; i < me.detalles.length; i++) {
        let element = me.detalles[i];
        if (element.id == id) {
          me.detalles.splice(i, 1);
          me.listDetalles.splice(i, 1);
          me.eliminarDetalles(element);
        }
      }

      if (me.listDetalles.length == 0) {
        me.campoBloqueado = false;
      } else {
        me.campoBloqueado = true;
      }
      me.calcularTotal();
      // me.detalles.forEach(element => {
      //     if (element.id == id) {
      //         me.detalles.slice
      //     }
      // })
    },
    setTipoPersona () {
      let me = this
      me.tipoPersona = document.getElementById(`select_tipopersona_${me.entidad}`).value
      // console.log('me.tipoPersona', me.tipoPersona)
      me.tipoVenta()
    },
    calcularTotal() {
      let me = this;
      let acum = 0;
      me.detalles.forEach((element) => {
        acum += element.cantidad * element.precio;
      });
      me.total = (parseFloat(acum) * 1000) / 1000;
      me.total = Math.round(me.total * 100) / 100;
      let val = document.getElementById(
        "select_tipodocumento_" + me.entidad
      ).value;
      if (val == "F" && me.afectoIgv == "10") {
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
    tipoVenta() {
      let me = this;
      let val = document.getElementById(
        "select_tipodocumento_" + me.entidad
      ).value;
     
      // console.log('tipoPersona', me.tipoPersona)
      let element = document.getElementById("documento_" + me.entidad);
      if (val == "F") {
        element.setAttribute("minlength", "11");
        element.setAttribute("maxlength", "11");
      } else {
        if (me.tipoPersona == 3) {
          element.setAttribute("minlength", "9");
          element.setAttribute("maxlength", "9");
        } else {
          element.setAttribute("minlength", "8");
          element.setAttribute("maxlength", "8");
        }
      }

      if (/*val == 'F' && me.is_igv &&*/ me.afectoIgv == "10") {
        me.subtotal = Math.round(parseFloat(me.total / 1.18) * 100) / 100;
        me.igv = Math.round(parseFloat(me.total - me.subtotal) * 100) / 100;
      } else {
        me.igv = 0;
        me.subtotal = me.total;
        me.metodoPago = "C";
      }
      element.value = "";
      document.getElementById("cliente_" + me.entidad).value = "";
      element.focus();
      this.getSeries(val);
    },
    validIgv() {
      let me = this;
      // let valAfecto = document.getElementById('select_igv_'+me.entidad).value
      // me.afectoIgv =  valAfecto
      // let element = document.getElementById('documento_'+me.entidad)
      if (/*val == 'F' && me.is_igv &&*/ me.afectoIgv == "10") {
        me.subtotal = Math.round(parseFloat(me.total / 1.18) * 100) / 100;
        me.igv = Math.round(parseFloat(me.total - me.subtotal) * 100) / 100;
      } else {
        me.igv = 0;
        me.subtotal = me.total;
      }
    },
    agregarCliente() {
      let me = this;
      me.isValidSession();

      let doc = document.getElementById("documento_" + me.entidad).value;
      me.documento = doc;
      this.$store.state.mostrarModal = !this.$store.state.mostrarModal;
      // alert(this.$store.state.mostrarModal)
      me.$refs.cliente.showModal(doc, 1);
    },
    verCliente() {
      let me = this;
      me.isValidSession();

      let doc = document.getElementById("documento_" + me.entidad).value;
      me.documento = doc;
      this.$store.state.mostrarModal = !this.$store.state.mostrarModal;
      me.$refs.cliente.showModal(doc, 2);

      // myWindow = window.open("/cliente/crear/"+seleccionado.trim(), "_blank", "toolbar=yes,scrollbars=yes,resizable=yes,top=500,left=500,width=500,height=1200");
      // myWindow.focus();
    },
    agregarDetalle() {
      let me = this;
      let ref = me.elementoSeleccionado;
      let id_ref = ref.id;
      if (ref.tipo == "Auto") {
        id_ref = `${ref.id}@${ref.lote_id}`;
      }

      let rpta = me.buscarInArray(id_ref, ref.tipo);

      let cantidad = document.getElementById("cantidad_" + me.entidad).value;
      document.getElementById("data-stock_" + me.entidad).innerHTML = "";
      let band = false;
      if (rpta) {
        if (ref.tipo != "Servicio") {
          if (cantidad > parseFloat(me.stockMax)) {
            document.getElementById("data-stock_" + me.entidad).innerHTML =
              '<div style = "margin-top:10px;"><div class="alert alert-warning text-left alert-dismissible" role="alert"><button type = "button" class="close" data-dismiss = "alert">&times;</button><strong>Cantidad Indicada No Disponible</strong></a></div ></div>';
          }

          let descripcion = `${ref.descripcion} ${ref.descripcionadicional}`;
          let moneda = document.getElementById(
            `select_moneda_${me.entidad}`
          ).value;
          let monto = ref.precioSoles;
          if (moneda == "USD") {
            monto = ref.precioDolares;
          }
          var elemento = {
            id: me.generarCorrelativo(),
            tipo: ref.tipo,
            precio: monto,
            descripcion: descripcion.substr(0, 495),
            stock: ref.stock,
            cantidad: cantidad,
            stockMax: me.stockMax,
            tiempo: ref.tiempo,
            idP: ref.id,
            idEnlace: 0,
            lote: ref.lote_id,
          };

          if (ref.tipo == "Auto") {
            let id_push = `${ref.id}@${ref.lote_id}`;
            me.listAutos.push(id_push);
          } else {
            me.listProductos.push(ref.id);
          }
          band = true;
        } else {
          let descripcion =
            ref.nombre +
            (ref.medida != "-" ? ", Medida: " + ref.medida : "") +
            (ref.tipollanta != "-"
              ? ", Tipo de Llanta: " + ref.tipollanta
              : "") +
            (ref.modelo != "-" ? ", Modelo: " + ref.modelo : "") +
            (ref.sistema != "-" ? " , Sistema: " + ref.sistema : "");

          let unidad = ref.tiempo.split(" ");
          let consta = 1;
          if (unidad[1] == "hr") {
            consta = 60;
          } else if (unidad[1] == "min") {
            consta = 1;
          } else if (unidad[1] == "sem") {
            consta = 60 * 7;
          } else {
            consta = 30 * 60 * 7;
          }

          let tot = parseFloat(unidad[0] * consta);

          var elemento = {
            id: me.generarCorrelativo(),
            tipo: ref.tipo,
            precio: ref.precioS,
            descripcion: descripcion.substr(0, 495),
            stock: cantidad,
            cantidad: cantidad,
            stockMax: cantidad,
            tiempo: tot,
            idP: ref.id,
            idEnlace: 0,
          };

          me.listServicios.push(ref.id);
          band = true;
        }

        if (band) {
          me.detalles.push(elemento);
          me.listDetalles.push(elemento.id);
          me.ultElemento = ref.id;
          me.producto = "";
          me.campoBloqueado = true;
          document.getElementById("cantidad_" + me.entidad).value = 1;
          me.calcularTotal();
          // me.calcularTiempo()
        }
      } else {
        document.getElementById("data-stock_" + me.entidad).innerHTML =
          '<div style = "margin-top:10px;"><div class="alert alert-warning text-left alert-dismissible" role="alert"><button type = "button" class="close" data-dismiss = "alert">&times;</button><strong>Auto ya antes Agregado</strong></a></div ></div>';
      }
      // console.log(me.listDetalles)
    },
    async getSeries(tipo_doc) {
      let response = await axios.get(`/seriesdocumentoauto/${tipo_doc}`);
      this.series = response.data.series;
      if (this.series.length > 0) {
        this.bloqueo_serie_combo = false;
      } else {
        this.bloqueo_serie_combo = true;
      }
    },
  },
  created() {
    this.$on("buscarCliente", function () {
      this.busquedaCliente();
    });
  },
  activated: async function () {
    let me = this;
    me.isValidSession();
    me.is_igv = await me.isIgv();
    this.$store.state.mostrarModal = !this.$store.state.mostrarModal;
    me.afectoIgv = "20";
    me.bandCliente = false;
    me.bandCliente02 = false;
    me.bandTienda = false;
    me.campoBloqueado = false;
    me.clienteId = 0;
    me.detalles = [];
    me.tipoPersona = "1"
    me.listDetalles = [];
    me.listServicios = [];
    me.listProductos = [];
    me.listAutos = [];
    me.listOrdenes = [];
    me.listCotizaciones = [];
    me.metodoPago = "C";
    me.tipoVentaV = "A";
    me.tipoComprobante = "";
    me.nroDias = "";
    me.moneda_s = "PEN";
    me.chkCaja = true;
    me.series = [];
    me.arrProducto = [];
    document.getElementById("select_tipodocumento_" + me.entidad).value = "";
    document.getElementById("select_tipo_operacion_" + me.entidad).value = "";
    document.getElementById("descripcion_" + me.entidad).value = "";
    document.getElementById("data-stock_" + me.entidad).innerHTML = "";
    document.getElementById("data-cliente_" + me.entidad).innerHTML = "";
    document.getElementById("data-error_" + me.entidad).innerHTML = "";
    document.getElementById("cliente_" + me.entidad).value = "";
    document.getElementById("documento_" + me.entidad).value = "";
    document.getElementById("observacion_" + me.entidad).value = "";
    document.getElementById("asesor_" + me.entidad).value = "";
    document.getElementById("select_forma_pago_" + me.entidad).value =
      "Efectivo";

    me.calcularTotal();

    me.cargarDatos();
  },
  mounted() {
    // this.getSeries()
    // console.log(this.tipoVentaV);
  },
  beforeDestroy: function () {
    let me = this;
    var element = document.getElementById("sec_" + me.entidad);
    element.classList.add("hidden");
    // this.$store.state.mostrarModal = false
  },
};
</script>
<style scoped>
.mr_p-0 {
  margin-right: 0.5rem !important;
}
.no-resize {
  resize: none;
}
.ocultar {
  display: none;
}
select {
  cursor: pointer;
}

.table-responsive {
  overflow: auto;
  padding: 0 1rem;
  scrollbar-color: #b46868 rgba(0, 0, 0, 0);
  scrollbar-width: thin;
}

thead {
  position: sticky;
  top: 0;
}
tfoot {
  position: sticky;
  bottom: 0;
}
.chk:after {
  width: 15px;
  height: 15px;
  border-radius: 15px;
  top: -2px;
  left: -1px;
  position: relative;
  background-color: #d1d3d1;
  content: "";
  display: inline-block;
  visibility: visible;
  border: 2px solid white;
  cursor: pointer;
}

.chk:checked:after {
  width: 15px;
  height: 15px;
  border-radius: 15px;
  top: -2px;
  left: -1px;
  position: relative;
  background-color: teal;
  content: "";
  display: inline-block;
  visibility: visible;
  border: 2px solid white;
}

table td {
  cursor: pointer;
}
</style>
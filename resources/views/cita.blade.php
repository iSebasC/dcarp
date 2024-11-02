<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agende su Cita | Carpio S.A.C</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-F3w7mX95PdgyTmZZMECAngseQB83DfGTowi0iMjiWaeVhAn4FJkqJByhZMI3AhiU" crossorigin="anonymous">
    <link rel="stylesheet" href="{{ asset('/css/styles.css') }}">
    <link rel="apple-touch-icon" href="{{asset('/images/logo_carpio.png')}}">
    <link rel="shortcut icon" type="image/x-icon" href="{{asset('/images/logo_carpio.png')}}">
</head>
<body>
    <header class="navbar navbar-light" style="background-color: #fff;">
        <div class="container">
          <a class="navbar-brand" href="#">
            <img src="{{ asset('/images/logo_carpio.png') }}" alt="Logo Carpio" width="90" height="50">
          </a>
        </div>
    </header>
    <div class="navbar navbar-dark bg-dark" style="height: 50px;"></div>
    
    <div class="container">
        <div class="row pt-4">
            <div class="col-md-12">
                <div class="card my-2">
                    <form id="formEnv" class="form" method="POST" action="/guardarcitapublic">
                    <div class="card-header">
                        Agende su Cita
                    </div>
                    <div class="card-body">
                        <h5 class="card-title">Datos del Encargado</h5>
                        <div class="row my-3">
                            <input type="hidden" name="id" value="0">
                            <div class="col-md-2 col-xs-12">
                                <label class="form-label" for="select_tipodocumento">Tipo de Persona <span class="text-danger">*</span></label>
                                <select class="form-select form-select-sm" onchange="tipoCliente(this);" name="tipodocumento" id="select_tipodocumento">
                                    <option value="B">Persona Natural</option>
                                    <option value="F">Persona Jurídica</option>
                                </select>
                            </div>
        
                            <div class="col-md-2 col-xs-12">
                                <div class="form-group">
                                    <label class="form-label" for="documento">Documento <span class="text-danger">*</span></label>
                                    <input type="text" id="documento" onkeypress="soloNumeros(event)" onkeyup="buscarCliente(event)" onchange="buscarCliente(event)" minlength="8" 
                                    maxlength="11" class="form-control form-control-sm" name="documento" onkeypress="soloLetras(event);">
                                </div>
                            </div>
                       
                            <div class="col-md-3 col-xs-12" id="apellidos_persona">
                                <div class="form-group">
                                    <label class="form-label" for="apellidos">Apellidos <span class="text-danger">*</span></label>
                                    <input type="text" id="apellidos" class="form-control form-control-sm" name="apellidos" onkeypress="soloLetras(event);">
                                </div>
                            </div>
                       
                            <div class="col-md-3 col-xs-12" id="nombres_persona">
                                <div class="form-group">
                                    <label for="nombres" class="form-label">Nombres <span class="text-danger">*</span></label>
                                    <input type="text" id="nombres" value="" 
                                    class="form-control form-control-sm" name="nombres" onkeypress="soloLetras(event);">
                                </div>
                            </div>
        
                            <div class="col-md-4 col-xs-12 ocultar" id="razon_social_persona">
                                <div class="form-group">
                                    <label class="form-label" for="razonSocial">Razón Social <span class="text-danger">*</span></label>
                                    <input type="text" id="razonSocial" class="form-control form-control-sm" name="razonSocial" 
                                    onkeypress="soloLetras(event);">
                                </div>
                            </div>
        
                            <div class="col-md-2 col-xs-12">
                                <div class="form-group">
                                    <label class="form-label" for="telefono">Teléfono <span class="text-danger">*</span></label>
                                    <input type="text" id="telefono" class="form-control form-control-sm" 
                                    name="telefono" minlength="6" maxlength="9" onkeypress="soloNumeros(event);">
                                </div>
                            </div>
                        </div>

                        <div class="row my-3">
                            <div class="col-md-3 col-xs-12">
                                <div class="form-group">
                                    <label class="form-label" for="correo">Correo Electrónico <span class="text-danger">*</span></label>
                                    <input type="email" id="correo" class="form-control form-control-sm" name="correo" >
                                </div>
                            </div>
        
                            <div class="col-md-12" id="data-cliente">
                            </div>
                        </div>

                        <hr/>
                        <h5 class="card-title">Datos del Auto</h5>
                        <div class="row my-3">
                            <div class="col-md-3 col-xs-12">
                                <label class="form-label" for="select_marca">Marca <span class="text-danger">*</span></label>
                                <select class="form-select form-select-sm" name="select_marca" onchange="marcaNueva(this)"
                                id="select_marca">
                                    <option disabled="" value="" selected="">Seleccione</option>
                                    <option value="otro">Indique otra</option>
                                </select>
                            </div>

                            <div class="col-md-3 col-xs-12 ocultar" id="marca_ocultar">
                                <div class="form-group">
                                    <label class="form-label" for="marca">Marca <span class="text-danger">*</span></label>
                                    <input type="text" id="marca" class="form-control form-control-sm"
                                    name="marca" maxlength="255">
                                </div>
                            </div>

                            <div class="col-md-3 col-xs-12">
                                <div class="form-group">
                                    <label class="form-label" for="modelo">Modelo <span class="text-danger">*</span></label>
                                    <input type="text" id="modelo" class="form-control form-control-sm" name="modelo" maxlength="255">
                                </div>
                            </div>

                            <div class="col-md-2 col-xs-12">
                                <div class="form-group">
                                    <label class="form-label" for="anio">Año <span class="text-danger">*</span></label>
                                    <input type="number" min="1800" value="2000" id="anio" 
                                    class="form-control form-control-sm text-center" name="anio">
                                </div>
                            </div>
                        </div>
                  
                        <div class="row my-1">
                            <div class="col-md-2 col-xs-12">
                                <div class="form-group">
                                    <label for="placa" class="form-label">Placa <span class="text-danger">*</span></label>
                                    <input type="text" id="placa" class="form-control form-control-sm text-center" 
                                    name="placa" maxlength="10" onkeypress="soloNumerosGuion(event);">
                                </div>
                            </div>
                    
                            <div class="col-md-2 col-xs-12">
                                <div class="form-group">
                                    <label for="kilometraje" class="form-label">Kilometraje <span class="text-danger">*</span></label>
                                    <input type="number" step="0.1" id="kilometraje" class="form-control form-control-sm text-center" name="kilometraje">
                                </div>
                            </div>
                        </div>
                        <hr/>
                       
                        <h5 class="card-title">Datos de Cita</h5>
                        <div class="row my-3">
                            <div class="col-md-3 col-xs-12">
                                <label class="form-label" for="select_tipo">T. de Servicio <span class="text-danger">*</span></label>
                                <select class="form-select form-select-sm" name="tipoServicio" id="select_tipo">
                                    <option value="" disabled="" selected="">Seleccione</option>
                                    <option value="MP">Mantenimiento</option>
                                    <option value="MC">Reparación</option>
                                </select>
                            </div>
                            
                            <div class="col-md-2 col-xs-12">
                                <div class="form-group">
                                    <label class="form-label" for="fecha">Fecha <span class="text-danger">*</span></label>
                                    <input type="date" id="fecha" value="" class="form-control form-control-sm text-center" 
                                    name="fecha">
                                </div>
                            </div>

                            <div class="col-md-2 col-xs-12">
                                <div class="form-group">
                                    <label class="form-label" for="hora">Hora <span class="text-danger">*</span></label>
                                    <input type="time" id="hora" class="form-control form-control-sm text-center" 
                                    name="hora">
                                </div>
                            </div>

                            <div class="col-md-2 col-xs-12">
                                <div class="form-group">
                                    <label class="form-label" for="duracion">Duración <small class="text-muted">(en min.)</small> <span class="text-danger">*</span></label>
                                    <input readOnly="" type="number" value="30" id="duracion" 
                                    class="form-control form-control-sm text-center" name="duracion" min="10">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-9 col-xs-12">
                                <div class="form-group">
                                    <label class="form-label" for="indicaciones">Indicaciones para su Reparación <span class="text-danger">*</span></label>
                                    <textarea id="indicaciones" class="form-control form-control-sm no-resize" rows="3" 
                                    name="indicaciones"></textarea>
                                </div>
                                <input type="hidden" name="con_cita" value="N">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12" id="data-error">
                            </div>
                        </div>
                    </div>
                    <div class="card-footer text-muted">
                        <div class="d-grid gap-2 col-6 mx-auto">
                            <button type="button" id="btnEnvio" onclick="enviarForm();" class="btn btn-outline-primary btn-block btn-md">
                                Registrar
                            </button>
                        </div>
                    </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js" integrity="sha384-W8fXfP3gkOKtndU4JGtKDvXbO53Wy8SZCQHczT5FMiiqmQfUpWbYdTil/SxwZgAN" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/js/bootstrap.min.js" integrity="sha384-skAcpIdS7UcVUC05LJ9Dxay8AXcDYfBJqt1CJ85S/CFujBsIzCIv+l9liuYLaMQ/" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

    <script src="{{ asset('/js/init-cita.js') }}"></script>
</body>
</html>
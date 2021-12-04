$(document).ready(function () {
    console.log('Hola de Jquery');

    //Call Back de todas las Tablas del sitema
    ReadAgentes();
    TablaChatAsignadoAgente();
    PlantillasMensajes();
    ReadAccesWebToken();
    TablaBackups();

    //Forms Para agregar datos al sitema
    FormIngresarAgente();
    AgregarToken();
    AgregarPlantillaMensaje();

    //Botonodes de Acciones
    EliminarPlantillaMensaje();
    EditarPlantillaMensaje();
    EliminarAgente();
    EditarAgente();
    EliminarToken();



    //Validacion de contraseñas iguales


});

let ReadAgentes = function () {
    let table = $('#TablaAgentes').DataTable({
        "destroy": true,
        "ajax": {
            "method": "POST",
            "url": "?controller=Datatable"
        },
        "columns": [
            { "data": "id" },
            { "data": "usuario" },
            { "data": "nombre" },
            { "data": "apellido" },
            { "data": "documento" },
            { "data": "telefono" },
            { "data": "correo" },

            { "defaultContent": "<button type='button' class='btn btn-primary btn-sm btnEditarAgente' id='btnEditarAgente' data-togle='modal' data-target='#Edit-Agentes'><i class='fas fa-edit'></i></button>	<button type='button' class=' btn btn-danger btn-sm btnEliminarAgente' data-toggle='modal' data-target='#modalEliminar' ><i class='fas fa-trash-alt'></i></button>" }
        ]
    });
}

//Mostrando tabla del dashboard

let TablaChatAsignadoAgente = function () {
    let table = $('#TableDasboard').DataTable({
        "ajax": {
            "method": "POST",
            "url": "?controller=TablaChatAsignadoAgente",
            "dataSrc": "",
        },

        "columns": [
            { "defaultContent": "<button type='submit' class='btn btn-success'><i class='fas fa-eye'></i></button>" },
            { "data": "nombre" },
            { "data": "apellido" },
            { "data": "usuario" },
            { "data": "ChatAbiertos" },
            { "data": "ChatPendiente" },
        ],

    });

}

//Mostrando Tabla de Mensajes
let PlantillasMensajes = function () {
    $.ajax({
        type: "GET",
        url: "?controller=MostrandoMensajeDespedida",
        success: function (Respuesta) {
            if (Respuesta != null) {
                var json = JSON.parse(Respuesta);
                var tabla = '';
                json.forEach(
                    Datos => {
                        tabla += `
                            <tr>
                                <td>${Datos.cuerpo}</td>
                                <td>${Datos.fecha}</td>
                                <td>${Datos.usuario}</td>
                                <td>
                                <button type="button" class="btn btn-danger btn-sm btnEliminar" id="btnEliminar" value="${Datos.id}">
                                    <span class="fas fa-trash-alt"></span>
                                </button>
                                <button type="button" class="btn btn-success btn-sm btnEditar">
                                 <span <i class="fas fa-edit" id="btnEditar" value="${Datos.id}"></i>
                                 </button>
                                </td>
                            </tr>
                            `
                    }
                );
                $('#plantillasmensajes').html(tabla);
            }
        }
    });
}

//Read AccesWebToken
let ReadAccesWebToken = function () {
    let table = $('#TablaTokenChatApi').DataTable({
        "destroy": true,
        "ajax": {
            "method": "POST",
            "url": "?controller=ReadAccesWebToken",
            "dataSrc": "",

        },
        "columns": [
            { "data": "Instance" },
            { "data": "Token" },
            { "defaultContent": "<center><button type='button' class='btn btn-primary '><i class='fas fa-edit'></i></button>	<button  class='btnEliminarToken btn btn-danger' data-toggle='modal' data-target='#modalEliminar' ><i class='fas fa-trash-alt'></i></button></center>" }
        ]
    });
}
let TablaBackups = function () {
    let table = $('#TablaBackups').DataTable({
        "destroy": true,
        "ajax": {
            "method": "POST",
            "url": "?controller=ReadBackups",
            "dataSrc": "",
        },
        "columns": [
            { "data": "id" },
            { "data": "nombre" },
            { "data": "tamano" },
            
            { "data": "fecha" },
            { "data": "usuario" },
            { "defaultContent": "<button type='button' class='btn btn-primary btn-sm btnEditarAgente' id='btnEditarAgente' data-togle='modal' data-target='#Edit-Agentes'><i class='fas fa-edit'></i></button>	<button type='button' class=' btn btn-danger btn-sm btnEliminarAgente' data-toggle='modal' data-target='#modalEliminar' ><i class='fas fa-trash-alt'></i></button>" }
        ]
    });
}

let FormIngresarAgente = function () {
    $('#btnActionForm').click(function (e) {
        e.preventDefault();
        let Formdata = $('#formAgregarUsuarios').serialize();
        $.ajax({
            type: "POST",
            url: "?controller=AgregarAgente",
            data: Formdata,

            success: function (Respuesta) {
                if (Respuesta == 'Agente Registrado Correctamente') {
                    Swal.fire({
                        title: 'Exito',
                        showClass: {
                            popup: 'animate__animated animate__fadeInDown'
                        },
                        hideClass: {
                            popup: 'animate__animated animate__fadeOutUp'
                        },
                        text: Respuesta,
                        icon: 'success',
                        position: 'center',
                    });
                } else if (Respuesta == 'Las contraseñas no coinciden') {
                    Swal.fire({
                        title: 'Oops...',
                        showClass: {
                            popup: 'animate__animated animate__fadeInDown'
                        },
                        hideClass: {
                            popup: 'animate__animated animate__fadeOutUp'
                        },
                        text: Respuesta,
                        icon: 'warning',
                        position: 'center',
                    });

                }
                else if (Respuesta == 'El usuario ya existe') {
                    Swal.fire({
                        title: 'Oops...',
                        showClass: {
                            popup: 'animate__animated animate__fadeInDown'
                        },
                        hideClass: {
                            popup: 'animate__animated animate__fadeOutUp'
                        },
                        text: Respuesta,
                        icon: 'error',
                        position: 'center',
                    });
                }
            },
            error: function (xhr, status, error) {
                console.log(xhr);
                console.log(status);
                console.log(error);
            }
        });
        //Limpiando vasillas y desmarcando checkbox
        $('#usuario').val('');
        $('#nombre').val('');
        $('#apellido').val('');
        $('#documento').val('');
        $('#admin').prop('checked', false);
        $('#master').prop('checked', false);
        $('#telefono').val('');
        $('#direccion').val('');
        $('#correo').val('');
        $('#password').val('');
        $('#ConfirmacionPassword').val('');
        ReadAgentes();
    });
}

let AgregarToken = function () {
    $('#btnIngresoAccesWebToken').click(function (e) {
        e.preventDefault();
        let form = $('#frmAccesWebToken').serialize();

        $.ajax({
            type: "POST",
            url: "?controller=InsertAccesWebToken",
            data: form,
            success: function (Respuesta) {
                if (Respuesta == 'Token Ingresado Correctamente') {
                    Swal.fire({
                        title: 'Exito',
                        text: Respuesta,
                        icon: 'success',
                        position: 'center',
                        showClass: {
                            popup: 'animate__animated animate__fadeInDown'
                        },
                        hideClass: {
                            popup: 'animate__animated animate__fadeOutUp'
                        },
                    });
                }
            },
            error: function (xhr, status, error) {
                console.log(xhr);
                console.log(status);
                console.log(error);
            }
        });
        $('#instancia').val('');
        $('#token').val('');
        ReadAccesWebToken();
    });
}

let AgregarPlantillaMensaje = function () {
    $('#btnAgregarMensaje').click(function (e) {
        e.preventDefault();

        var form = $('#formPlantillasMensaje').serialize();

        $.ajax({
            type: "POST",
            url: "?controller=CreateMensajeDespedida",
            data: form,
            success: function (Respuesta) {
                if (Respuesta == 'Mensaje Agregado Exitosamente') {
                    Swal.mixin({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true,

                    }).fire({
                        icon: 'success',
                        title: 'Exito',
                        text: Respuesta,
                        showClass: {
                            popup: 'animate__animated animate__fadeInDown'
                        },
                        hideClass: {
                            popup: 'animate__animated animate__fadeOutUp'
                        },


                    });
                } else if (Respuesta == 'Algo salio mal') {
                    Swal.mixin({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true,

                    }).fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: Respuesta,
                        showClass: {
                            popup: 'animate__animated animate__fadeInDown'
                        },
                        hideClass: {
                            popup: 'animate__animated animate__fadeOutUp'
                        },


                    });
                }
            },
            error: function (xhr, status, error) {
                console.log(xhr);
                console.log(status);
                console.log(error);
            }
        });
        $('#text-area-mensajes').val('');
        PlantillasMensajes();


    });
}

let EliminarPlantillaMensaje = function () {
    /* $('#btnEliminar').click(function (e) {
        e.preventDefault();
        var form = $('#FormMensajePlantilla').serialize();
        $.ajax({
            type: "POST",
            url: "?controller=DeleteMensajeDespedida",
            data: form,
            success: function (Respuesta) {
                console.log(Respuesta);
            },
            error: function (xhr, status, error) {
                console.log(xhr);
                console.log(status);
                console.log(error);
            }
        });
        PlantillasMensajes();
    }); */

    $(document).on('click', '.btnEliminar', function (e) {
        e.preventDefault();
        console.log($(this).parent().first());

    });
}

let EditarPlantillaMensaje = function () {
    $(document).on('click', '.btnEditar', function (e) {
        e.preventDefault();
        console.log('Editar');
    });
}

let EliminarAgente = function () {
    $(document).on('click', '.btnEliminarAgente', function (e) {
        e.preventDefault();
        let id = $(this).parent().parent().children('td:eq(0)').text();
        console.log(id);
        $.ajax({
            type: "POST",
            url: "?controller=DeleteAgentes",
            data: { id: id },
            success: function (Respuesta) {
                if (Respuesta == 'El Agente ha sido eliminado') {
                    Swal.fire({
                        title: '¿Estas Seguro?',
                        text: "¡No podras revertir esto!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Si, Eliminarlo!',
                        showClass: {
                            popup: 'animate__animated animate__fadeInDown'
                        },
                        hideClass: {
                            popup: 'animate__animated animate__fadeOutUp'
                        },

                    }).then((result) => {
                        if (result.value) {
                            Swal.fire({
                                title: 'Eliminado!',
                                text: Respuesta,
                                icon: 'success',
                                showClass: {
                                    popup: 'animate__animated animate__fadeInDown'
                                },
                                hideClass: {
                                    popup: 'animate__animated animate__fadeOutUp'
                                },
                                position: 'center',
                            });
                            ReadAgentes();
                        }
                    })
                }

            }

        })
    });
}
let EditarAgente = function () {
    $(document).on('click', '.btnEditarAgente', function (e) {
        e.preventDefault();


    });
}

let EliminarToken = function () {
    $(document).on('click', '.btnEliminarToken', function (e) {
        e.preventDefault();
        let id = $(this).parent().parent().children('td:eq(0)').text();
        console.log(id);
        $.ajax({
            type: "POST",
            url: "?controller=DeleteAccesWebToken",
            data: { id: id },
            success: function (Respuesta) {
                if (Respuesta == 'Token Eliminado con Exito') {
                    Swal.fire({
                        title: '¿Estas Seguro?',
                        text: "¡No podras revertir esto!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Si, Eliminarlo!',
                        showClass: {
                            popup: 'animate__animated animate__fadeInDown'
                        },
                        hideClass: {
                            popup: 'animate__animated animate__fadeOutUp'
                        },

                    }).then((result) => {
                        if (result.value) {
                            Swal.fire({
                                title: 'Eliminado!',
                                text: Respuesta,
                                icon: 'success',
                                showClass: {
                                    popup: 'animate__animated animate__fadeInDown'
                                },
                                hideClass: {
                                    popup: 'animate__animated animate__fadeOutUp'
                                },
                                position: 'center',
                            });
                            ReadAccesWebToken();
                        }
                    })
                }

            }

        })
    });




}
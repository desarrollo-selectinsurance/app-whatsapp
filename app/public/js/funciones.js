$(document).ready(function () {
    console.log('Hola desde jquery');

    //$.ajaxSetup({ "cache": false });//Manejo de cache de Jquery


    EnviarMensajesChat();
    SettIntervals();
    MostrarMensajesDespedida();
    DeleteMensajeDespedida();
    ValidacionCantidadMaximaCaracteres();
    InsertarMensajeDespedida();
    //MostrarConversacionDataTable();
    IngresarAgente();
    ReadAgentes();
    CambiarContrasena();
    ReadAccesWebToken();
    IngresoAccessWebToken();
    //ActivarEmotes();
    ReadTransferenciaChat();
    CreateTransferirChat();
    Tooltip();
    SearchDialogs();
    MostrarModalTablaChatAcumulado();
    /* MostrarModalTablaChatCerrados(); */
    MostrarModalTablaChatAsignados();
    FiltrandoSalaNav();
});
$(document).ready(function () {
    $('#codigopais').select2({
        theme: "bootstrap-5",



    });//llamando al componente select de la libreria select2 de jquery

});
const searchBar = document.querySelector(".search input"),
    searchIcon = document.querySelector(".search button"),
    usersList = document.querySelector(".users-list");
searchIcon.onclick = () => {
    searchBar.classList.toggle("show");
    searchIcon.classList.toggle("active");
    searchBar.focus();
    if (searchBar.classList.contains("active")) {
        searchBar.value = "";
        searchBar.classList.remove("active");
    }
}

//AQUI ENCONTRARAS TODOS LOS SETINTERVAL
var SettIntervals = function () {

    setInterval('MostrarMensajesChat()', 3000);
    var interval = setInterval('MostrarMensajesChat()', 3000);
    clearInterval(interval);

    setInterval('UpdateInstance()', 360000);
    var interval = setInterval('UpdateInstance()', 360000);
    clearInterval(interval);

    setInterval('MostrarCantidadSalasChatAsignadas()', 3000);
    var interval = setInterval('MostrarCantidadSalasChatAsignadas()', 3000);
    clearInterval(interval);

    setInterval('MostrarCantidadSalasChat()', 3000);
    var interval = setInterval('MostrarCantidadSalasChat()', 3000);
    clearInterval(interval);

    setInterval('MostrarCantidadSalasChatAbiertas()', 3000);
    var interval = setInterval('MostrarCantidadSalasChatAbiertas()', 3000);
    clearInterval(interval);

    setInterval('MostrarCantidadSalasChatCerradas()', 3000);
    var interval = setInterval('MostrarCantidadSalasChatCerradas()', 3000);
    clearInterval(interval);

    setInterval('TablaChatAsignadoAgente()', 30000);
    var interval = setInterval('TablaChatAsignadoAgente()', 30000);
    clearInterval(interval);

    setInterval('ReadConversacionDialogSeleccionadoTablaConversaciones()', 1000);
    var interval = setInterval('ReadConversacionDialogSeleccionadoTablaConversaciones()', 1000);
    clearInterval(interval);

    setInterval('MostrarModalTablaChatAbierto()', 3000);
    var interval = setInterval('MostrarModalTablaChatAbierto()', 3000);
    clearInterval(interval);
}
//









//AQUI COMIENZAN LAS FUNCTIONES DE LAS TABLAS DEL MODAL DE LOS CONTEOS
//Funcion para Mostrar Tabla en conteo total chat
var MostrarModalTablaChatAcumulado = function () {
    $('#FiltroTablaTotal').keyup(function (e) {
        var form = $('#frmFiltrarTotalSala').serialize();
        $.ajax({
            type: "POST",
            url: "?controller=MostrarTablaChatAcumulado",
            data: form,
            success: function (Respuesta) {
                //console.log(Respuesta);
                var json = JSON.parse(Respuesta);
                if (json !== 'null') {
                    var tbody = '';
                    json.forEach(
                        consulta => {
                            if (consulta.Asignador == null) {
                                var SinAsignar = 'Sin Asignar';
                            } else {
                                var SinAsignar = consulta.Asignador;
                            }

                            if (consulta.idAgentes == null) {
                                var idAgentes = 'Sin Asignar';
                            } else {
                                var idAgentes = consulta.idAgentes;
                            }
                            tbody += `
                                <tr>
                                    <td>${consulta.id}</td>
                                    <td>${consulta.name}</td>
                                    <td><img src="${consulta.image}" class="img-thumbnail rounded" width="40px"></td>
                                    <td>${SinAsignar}</td>
                                    <td>${idAgentes}</td>
                                    <td>
                                    <form action="?controller=ConsultandoSalaDesdeModalTotal" method="post">
                                    <button type="submit" value="${consulta.id}" class="btn btn-success btn-sm" name="btnIdConsultarSala[]"><i class="far fa-share-square"></i></button></input>
                                    </form>
                                    </td>
                                </tr>
                                `;
                        }
                    )
                    $('#TablaChatAcumulado').html(tbody);
                }
            },
            error: function (xhr, status, error) {
                console.log(xhr);
                console.log(status);
                console.log(error);
            }
        });
    });

    $.ajax({
        type: "POST",
        url: "?controller=MostrarTablaChatAcumulado",
        success: function (Respuesta) {
            //console.log(Respuesta);
            var json = JSON.parse(Respuesta);
            if (json !== 'null') {
                var tbody = '';
                json.forEach(
                    consulta => {
                        if (consulta.Asignador == null) {
                            var SinAsignar = 'Sin Asignar';
                        } else {
                            var SinAsignar = consulta.Asignador;
                        }

                        if (consulta.idAgentes == null) {
                            var idAgentes = 'Sin Asignar';
                        } else {
                            var idAgentes = consulta.idAgentes;
                        }
                        tbody += `
                            <tr>
                                <td>${consulta.id}</td>
                                <td>${consulta.name}</td>
                                <td><img src="${consulta.image}" class="img-thumbnail rounded" width="40px"></td>
                                <td>${SinAsignar}</td>
                                <td>${idAgentes}</td>
                                <td>
                                <form action="?controller=ConsultandoSalaDesdeModalTotal" method="post">
                                <button type="submit" value="${consulta.id}" class="btn btn-success btn-sm" name="btnIdConsultarSala[]"><i class="far fa-share-square"></i></button></input>
                                </form>
                                </td>
                            </tr>
                            `;
                    }
                )
                $('#TablaChatAcumulado').html(tbody);
            }
        },
        error: function (xhr, status, error) {
            console.log(xhr);
            console.log(status);
            console.log(error);
        }
    });
}

//Funcion para mostrar Tabla en conteo Abierto chat
var MostrarModalTablaChatAbierto = function () {
    $('#FiltroTablaAbiertos').keyup(function (e) {
        var frm = $('#frmFiltrarAbiertosSala').serialize();

        $.ajax({
            type: "POST",
            url: "?controller=MostrarTablaChatAbiertos",
            data: frm,
            success: function (Respuesta) {
                //console.log(Respuesta);
                var json = JSON.parse(Respuesta);
                if (json !== 'null') {
                    var tbody = '';
                    json.forEach(
                        consulta => {
                            if (consulta.Asignador == null) {
                                var SinAsignar = 'Sin Asignar';
                            } else {
                                var SinAsignar = consulta.Asignador;
                            }

                            if (consulta.idAgentes == null) {
                                var SinAgentes = 'Sin Agentes';
                            } else {
                                var SinAgentes = consulta.idAgentes;
                            }
                            tbody += `
                                <tr>
                                    <td>${consulta.id}</td>
                                    <td>${consulta.name}</td>
                                    <td><img src="${consulta.image}" class="img-thumbnail rounded" width="40px"></td>
                                    <td>${SinAsignar}</td>
                                    <td>${SinAgentes}</td>
                                    <td>
                                    <form action="?controller=ConsultandoSalaDesdeModalTotal" method="post">
                                    <button type="submit" value="${consulta.id}" class="btn btn-success btn-sm" name="btnIdConsultarSala[]"><i class="far fa-share-square"></i></button></input>
                                    </form>
                                    </td>
                                </tr>
                                `;
                        }
                    )
                    $('#TablaChatAbiertosAcumulado').html(tbody);
                }
            },
            error: function (xhr, status, error) {
                console.log(xhr);
                console.log(status);
                console.log(error);
            }
        });
    });

    $.ajax({
        type: "POST",
        url: "?controller=MostrarTablaChatAbiertos",
        success: function (Respuesta) {
            //console.log(Respuesta);
            var json = JSON.parse(Respuesta);
            if (json !== 'null') {
                var tbody = '';
                json.forEach(
                    consulta => {
                        if (consulta.Asignador == null) {
                            var SinAsignar = 'Sin Asignar';
                        } else {
                            var SinAsignar = consulta.Asignador;
                        }

                        if (consulta.idAgentes == null) {
                            var SinAgentes = 'Sin Agentes';
                        } else {
                            var SinAgentes = consulta.idAgentes;
                        }
                        tbody += `
                            <tr>
                                <td>${consulta.id}</td>
                                <td>${consulta.name}</td>
                                <td><img src="${consulta.image}" class="img-thumbnail rounded" width="40px"></td>
                                <td>${SinAsignar}</td>
                                <td>${SinAgentes}</td>
                                <td>
                                <form action="?controller=ConsultandoSalaDesdeModalTotal" method="post">
                                <button type="submit" value="${consulta.id}" class="btn btn-success btn-sm" name="btnIdConsultarSala[]"><i class="far fa-share-square"></i></button></input>
                                </form>
                                </td>
                            </tr>
                            `;
                    }
                )
                $('#TablaChatAbiertosAcumulado').html(tbody);
            }
        },
        error: function (xhr, status, error) {
            console.log(xhr);
            console.log(status);
            console.log(error);
        }
    });
}

//Funcion para mostrar Tabla en conteo Cerrados chat
/* var MostrarModalTablaChatCerrados = function () {
    $('#FiltroTablaCerrados').keyup(function (e) {
        var form = $('#frmFiltrarCerradosSala').serialize();
        $.ajax({
            type: "POST",
            url: "?controller=MostrarTablaChatCerrados",
            data: form,
            success: function (Respuesta) {
                //console.log(Respuesta);
                var json = JSON.parse(Respuesta);
                if (json !== 'null') {
                    var tbody = '';
                    json.forEach(
                        consulta => {
                            if (consulta.Asignador == null) {
                                var SinAsignar = 'Sin Asignar';
                            } else {
                                var SinAsignar = consulta.Asignador;
                            }

                            if (consulta.idAgentes == null) {
                                var SinAgentes = 'Sin Agentes';
                            } else {
                                var SinAgentes = consulta.idAgentes;
                            }
                            tbody += `
                                <tr>
                                    <td>${consulta.id}</td>
                                    <td>${consulta.name}</td>
                                    <td><img src="${consulta.image}" class="img-thumbnail rounded" width="40px"></td>
                                    <td>${SinAsignar}</td>
                                    <td>${SinAgentes}</td>
                                    <td>
                                    <form action="?controller=ConsultandoSalaDesdeModalTotal" method="post">
                                    <button type="submit" value="${consulta.id}" class="btn btn-success btn-sm" name="btnIdConsultarSala[]"><i class="far fa-share-square"></i></button></input>
                                    </form>
                                    </td>
                                </tr>
                                `;

                        });
                    $('#TablaChatCerradosAcumulado').html(tbody);
                }
            },
            error: function (xhr, status, error) {
                console.log(xhr);
                console.log(status);
                console.log(error);
            }
        });


    });
    $.ajax({
        type: "POST",
        url: " ",
        success: function (Respuesta) {
            //console.log(Respuesta);
            var json = JSON.parse(Respuesta);
            if (json !== 'null') {
                var tbody = '';
                json.forEach(
                    consulta => {
                        if (consulta.Asignador == null) {
                            var SinAsignar = 'Sin Asignar';
                        } else {
                            var SinAsignar = consulta.Asignador;
                        }

                        if (consulta.idAgentes == null) {
                            var SinAgentes = 'Sin Agentes';
                        } else {
                            var SinAgentes = consulta.idAgentes;
                        }
                        tbody += `
                            <tr>
                                <td>${consulta.id}</td>
                                <td>${consulta.name}</td>
                                <td><img src="${consulta.image}" class="img-thumbnail rounded" width="40px"></td>
                                <td>${SinAsignar}</td>
                                <td>${SinAgentes}</td>
                                <td>
                                <form action="?controller=ConsultandoSalaDesdeModalTotal" method="post">
                                <button type="submit" value="${consulta.id}" class="btn btn-success btn-sm" name="btnIdConsultarSala[]"><i class="far fa-share-square"></i></button></input>
                                </form>
                                </td>
                            </tr>
                            `;

                    });
                $('#TablaChatCerradosAcumulado').html(tbody);
            }
        },
        error: function (xhr, status, error) {
            console.log(xhr);
            console.log(status);
            console.log(error);
        }
    });
} */

//Funcion para mostrar Tabla en conteo Asignados chat
var MostrarModalTablaChatAsignados = function () {
    $('#FiltroTablaAsignados').keyup(function (e) {
        var form = $('#frmFiltrarAsignadosSala').serialize();
        $.ajax({
            type: "POST",
            url: "?controller=MostrarTablaChatAsignados",
            data: form,
            success: function (Respuesta) {
                //console.log(Respuesta);
                var json = JSON.parse(Respuesta);
                if (json !== 'null') {
                    var tbody = '';
                    json.forEach(
                        consulta => {
                            if (consulta.Asignador == null) {
                                var SinAsignar = 'Sin Asignar';
                            } else {
                                var SinAsignar = consulta.Asignador;
                            }

                            if (consulta.idAgentes == null) {
                                var SinAgentes = 'Sin Agentes';
                            } else {
                                var SinAgentes = consulta.idAgentes;
                            }
                            tbody += `
                                <tr>
                                    <td>${consulta.id}</td>
                                    <td>${consulta.name}</td>
                                    <td><img src="${consulta.image}" class="img-thumbnail rounded" width="40px"></td>
                                    <td>${SinAsignar}</td>
                                    <td>${SinAgentes}</td>
                                    <td>
                                    <form action="?controller=ConsultandoSalaDesdeModalTotal" method="post">
                                    <button type="submit" value="${consulta.id}" class="btn btn-success btn-sm" name="btnIdConsultarSala[]"><i class="far fa-share-square"></i></button></input>
                                    </form>
                                    </td>
                                </tr>
                                `;

                        });
                    $('#TablaChatAsignadosAcumulado').html(tbody);
                }
            },
            error: function (xhr, status, error) {
                console.log(xhr);
                console.log(status);
                console.log(error);
            }
        });


    });
    $.ajax({
        type: "POST",
        url: "?controller=MostrarTablaChatAsignados",
        success: function (Respuesta) {
            //console.log(Respuesta);
            var json = JSON.parse(Respuesta);
            if (json !== 'null') {
                var tbody = '';
                json.forEach(
                    consulta => {
                        if (consulta.Asignador == null) {
                            var SinAsignar = 'Sin Asignar';
                        } else {
                            var SinAsignar = consulta.Asignador;
                        }

                        if (consulta.idAgentes == null) {
                            var SinAgentes = 'Sin Agentes';
                        } else {
                            var SinAgentes = consulta.idAgentes;
                        }
                        tbody += `
                            <tr>
                                <td>${consulta.id}</td>
                                <td>${consulta.name}</td>
                                <td><img src="${consulta.image}" class="img-thumbnail rounded" width="40px"></td>
                                <td>${SinAsignar}</td>
                                <td>${SinAgentes}</td>
                                <td>
                                <form action="?controller=ConsultandoSalaDesdeModalTotal" method="post">
                                <button type="submit" value="${consulta.id}" class="btn btn-success btn-sm" name="btnIdConsultarSala[]"><i class="far fa-share-square"></i></button></input>
                                </form>
                                </td>
                            </tr>
                            `;

                    });
                $('#TablaChatAsignadosAcumulado').html(tbody);
            }
        },
        error: function (xhr, status, error) {
            console.log(xhr);
            console.log(status);
            console.log(error);
        }
    });
}
//




//Funcion para personalizar el tooltip
var Tooltip = function () {

    //ToolTips de las Cards del Dashboard
    var TotalSalas = document.getElementById('TotalSalas')
    if (TotalSalas != null) {
        var tooltip = new bootstrap.Tooltip(TotalSalas, {
            boundary: document.body, // or document.querySelector('#boundary')
            template: '<div class="tooltip TooltipColorVerde" role="tooltip"><div class="tooltip-arrow"></div><div class="tooltip-inner"></div></div>'
        });
    }

    var cardAbiertos = document.getElementById('SalasAbiertas')
    if (cardAbiertos != null) {
        var tooltip = new bootstrap.Tooltip(cardAbiertos, {
            boundary: document.body, // or document.querySelector('#boundary')
            template: '<div class="tooltip TooltipColorVerde" role="tooltip"><div class="tooltip-arrow"></div><div class="tooltip-inner"></div></div>'
        });
    }


    var cardCerrados = document.getElementById('SalasCerradas')
    if (cardCerrados != null) {
        var tooltip = new bootstrap.Tooltip(cardCerrados, {
            boundary: document.body, // or document.querySelector('#boundary')
            template: '<div class="tooltip TooltipColorVerde" role="tooltip"><div class="tooltip-arrow"></div><div class="tooltip-inner"></div></div>'
        });
    }

    var cardAsignados = document.getElementById('SalasAsignadas')
    if (cardAsignados != null) {
        var tooltip = new bootstrap.Tooltip(cardAsignados, {
            boundary: document.body, // or document.querySelector('#boundary')
            template: '<div class="tooltip TooltipColorVerde" role="tooltip"><div class="tooltip-arrow"></div><div class="tooltip-inner"></div></div>'
        });
    }

    var carSeguimiento = document.getElementById('carSeguimiento')
    if (carSeguimiento != null) {
        var tooltip = new bootstrap.Tooltip(carSeguimiento, {
            boundary: document.body, // or document.querySelector('#boundary')
            template: '<div class="tooltip TooltipColorVerde" role="tooltip"><div class="tooltip-arrow"></div><div class="tooltip-inner"></div></div>'
        });
    }
    //

    //Tooltips de el menu de la Derecha
    var OpcionDashBoardMenu = document.getElementById('OpcionDashBoardMenu')
    if (OpcionDashBoardMenu != null) {
        var tooltip = new bootstrap.Tooltip(OpcionDashBoardMenu, {
            boundary: document.body, // or document.querySelector('#boundary')
            template: '<div class="tooltip TooltipColorAzul" role="tooltip"><div class="tooltip-arrow"></div><div class="tooltip-inner"></div></div>'
        });
    }

    var OpcionMensajeFinalMenu = document.getElementById('OpcionMensajeFinalMenu')
    if (OpcionMensajeFinalMenu != null) {
        var tooltip = new bootstrap.Tooltip(OpcionMensajeFinalMenu, {
            boundary: document.body, // or document.querySelector('#boundary')
            template: '<div class="tooltip TooltipColorAzul" role="tooltip"><div class="tooltip-arrow"></div><div class="tooltip-inner"></div></div>'
        });
    }

    var OpcionAddWhatsappMenu = document.getElementById('OpcionAddWhatsappMenu')
    if (OpcionAddWhatsappMenu != null) {
        var tooltip = new bootstrap.Tooltip(OpcionAddWhatsappMenu, {
            boundary: document.body, // or document.querySelector('#boundary')
            template: '<div class="tooltip TooltipColorAzul" role="tooltip"><div class="tooltip-arrow"></div><div class="tooltip-inner"></div></div>'
        });
    }
    //

}

//Funcion para filtrar salas desde el Nav
// el nombre de la funcion keyup es capturar las letras del input tecleabdo, cada pulsacion manda lls datos al controlador y el controlador hace una consulta con un like, y si no consulta nada
var FiltrandoSalaNav = function () {
    $('#filtrarNav').keyup(function (e) {
        var frm = $('#frmFiltroDialogNav').serialize();//aca esta capturando todo lo dem formularioy lo edta guardando en la variable
        $.ajax({
            type: "POST",
            url: "?controller=FiltrandoSalaNav",
            data: frm,// aca se esta enviando los datos capturados del formulario a la url haga un curso de jquery co para que entienda mejor y pueda adaptarlo ya que te va tocar cambiar el html de las peticiones ajax
            success: function (Respuesta) {
                //console.log(Respuesta);
                var json = JSON.parse(Respuesta);
                if (json.name === 'No existe Token') {
                    Swal.fire(
                        'The Internet?',
                        'That thing is still around?',
                        'question'
                    )
                } else {
                    let data = '';
                    $.each(json, function (i, consulta) {
                        var urlImage = '';
                        if (consulta.image !== 'null') {
                            urlImage = consulta.image;
                        } else {
                            urlImage = 'https://img.icons8.com/office/100/000000/box-important--v3.gif';
                        }
                        console.log(urlImage);
                        data += ` <a href="chat.php?user_id=${consulta.id}">
                        <div class="content">
                        <img src="${consulta.image}" alt="">
                        <div class="details">
                            <span>${consulta.name}</span>
                            
                        </div>
                        </div>
                        <div class="status-dot"><i class="fas fa-circle"></i></div>
                    </a>
                    `;
                    });
                    usersList.innerHTML = data;
                }
            },
            error: function (xhr, status, error) {
                console.log(xhr);
                console.log(status);
                console.log(error);
            }
        });
    })
    //mire por ejemplo aca, estoy haciendo un ajax y con la respuesta estoy pintando un htmles la tabla de token
    // sk no consulta nada automaticamente se ejecuta este otro ajax que es un select sin un where
    $.ajax({
        type: "POST",
        url: "?controller=FiltrandoSalaNav",
        success: function (Respuesta) {
            //console.log(Respuesta);
            var json = JSON.parse(Respuesta);

            if (json.name === 'No existe Token') {
                const Toast = Swal.mixin({
                    toast: true,
                    position: 'top',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true,
                    didOpen: (toast) => {
                        toast.addEventListener('mouseenter', Swal.stopTimer)
                        toast.addEventListener('mouseleave', Swal.resumeTimer)
                    }
                })

                Toast.fire({
                    icon: 'warning',
                    title: json.name,
                    text: 'Verifica si has agregado tokens en en el sistema',
                    showClass: {
                        popup: 'animate__animated animate__fadeInDown'
                    },
                    hideClass: {
                        popup: 'animate__animated animate__fadeOutUp'
                    }

                })


            } else {
                let data = '';
                //console.log(json);
                $.each(json.dialogs, function (i, consulta) {
                    data += `
                    <a href="chat.php?user_id=' ${consulta.id}'">
                    <div class="content">
                    <img src="${consulta.image}" alt="">
                    <div class="details">
                        <span>${consulta.name}</span>
                        
                    </div>
                    </div>
                    <div class="status-dot ' . $offline . '"><i class="fas fa-circle"></i></div>
                </a>
`;

                });
                usersList.innerHTML = data;
            }
        },
        error: function (xhr, status, error) {
            console.log(xhr);
            console.log(status);
            console.log(error);
        }
    });
}


//Function para reiniciar instancia de whatsapp para actualizar todos los perfiles y fotos
var UpdateInstance = function () {
    $.ajax({
        type: "POST",
        url: "?controller=ReiniciarEstancia",
        success: function (Respuesta) {
            console.log('Reinicio Exitoso de la instancia ' + Respuesta);
        },
        error: function (xhr, status, error) {
            console.log(xhr);
            console.log(status);
            console.log(error);
        }
    });
}

//Funcion para mostrar Datatable con los Agentes por Ajax
var ReadAgentes = function () {
    var table = $('#TablaAgentes').DataTable({
        "ajax": {
            "method": "POST",
            "url": "?controller=Datatable"
        },

        "columns": [
            { "data": "id" },
            { "data": "usuario" },
            { "data": "nombre" },
            { "data": "apellido" },
            { "data": "password" },
            { "data": "documento" },
            { "data": "telefono" },
            { "data": "correo" },
            { "data": "admin" },
            { "defaultContent": "<button type='button' class='btn btn-primary btn-sm btnEditar' id='btnEliminarMensajeDespedida'><i class='fas fa-edit'></i></button>	<button type='button' class=' btn btn-danger btn-sm btnBorrar' data-toggle='modal' data-target='#modalEliminar' ><i class='fas fa-trash-alt'></i></button>" }


        ]
    });
}

//Borrar
$(document).on("click", ".btnBorrar", function () {
    let fila = $(this);
    id = parseInt($(this).closest('tr').find('td:eq(0)').text());
    respuesta = Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire(
                'Deleted!',
                'Your file has been deleted.',
                'success'
            )
        }
    })

    if (respuesta) {

        $.ajax({
            url: "?controller=DeleteAgentes",
            type: "POST",
            data: { id: id },

        });

    }
});
//Ingresar Agente por ajax en el boton de dicho formulario
let IngresarAgente = function () {
    $('#btnRegistrarAgente').click(function (e) {
        e.preventDefault();


        let Formulario = $('#frmIngresarAgente').serialize();
        $.ajax({
            type: "POST",
            url: "?controller=AgregarAgente",
            data: Formulario,
            success: function (Respuesta) {
                $('#RespuestaIngresoAgentes').css('color', 'Green').html(Respuesta);
            },
            error: function (xhr, status, error) {
                console.log(xhr);
                console.log(status);
                console.log(error);
            }
        });

        //Limpiando vasillas y desmarcando checkbox
        $('#user').val('');
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
    });


}


//Funcion para Validar que las contraseñas coincidan
var CambiarContrasena = function () {
    $('#ConfirmarNuevaContrasena').keyup(function (e) {
        var pass1 = $('#NuevaContrasena').val();
        var pass2 = $('#ConfirmarNuevaContrasena').val();
        if (pass1 == pass2) {
            $('#MensajeCoincidencia').css('color', 'Green').html('Coinciden');
        } else if (pass1 != pass2) {
            $('#MensajeCoincidencia').css('color', 'Red').html('No Coinciden');
        }
    });
}


//Read AccesWebToken
var ReadAccesWebToken = function () {
    var table = $('#TablaTokenChatApi').DataTable({
        "ajax": {
            "method": "POST",
            "url": "?controller=ReadAccesWebToken",
            "dataSrc": "",

        },
        "columns": [
            { "data": "Instance" },
            { "data": "Token" },
            { "defaultContent": "<center><button type='button' class='btn btn-primary'><i class='fas fa-edit'></i></button>	<button type='button' class='eliminar btn btn-danger' data-toggle='modal' data-target='#modalEliminar' ><i class='fas fa-trash-alt'></i></button></center>" }
        ]
    });
}


//Ingreso de AccesWebToken
var IngresoAccessWebToken = function () {
    $('#btnIngresoAccesWebToken').click(function (e) {
        e.preventDefault();
        var form = $('#frmAccesWebToken').serialize();

        $.ajax({
            type: "POST",
            url: "?controller=InsertAccesWebToken",
            data: form,
            success: function (Respuesta) {
                $('#RespuestaIngresoToken').html(Respuesta)
                console.log(Respuesta);
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


//Activar Emotes
var ActivarEmotes = function () {
    $(function () {
        window.emojiPicker = new EmojiPicker({
            emojiable_selector: '[data-emojiable=true]',
            assetsPath: 'app/master/views/assets/Emoji/img',
            popupButtonClasses: 'icon-smile'
        });
        window.emojiPicker.discover();
    });
}











//TODO LO RELACIONADO CON LA TRANSFERENCIA DE CHAT
//Consultar tabla para transferir chat
var ReadTransferenciaChat = function () {
    var table = $('#TablaTransferirChat').DataTable({
        "ajax": {
            "method": "POST",
            "url": "ConsultandoUsuarioATransferir"
        },
        "columns": [
            { "data": "id" },
            { "data": "nombre" },
            { "data": "apellido" },
            { "data": "usuario" }
            //{ "data": "admin" }
        ]
    });
    /*$.ajax({
        type: "GET",
        url: "?controller=ConsultandoUsuarioATransferir",
        success: function (Respuesta) {
            var json = JSON.parse(Respuesta);
            var tbody = '';
            json.forEach(
                consulta => {
                    tbody += `
                    <tr>
                        <td><input type="checkbox" name="idAgenteTransferir[]" value="${consulta.id}" class="form-check-input"></td>
                        <td>${consulta.nombre}</td>
                        <td>${consulta.apellido}</td>
                        <td>${consulta.usuario}</td>
                    </tr>
                    `;
                }
            );
            $('#TablaTransferirChat').html(tbody);
        }
    });*/
};


//Transferir Sala a Agente
var CreateTransferirChat = function () {
    $('#btnTransferirChat').click(function (e) {
        e.preventDefault();
        $('#RespuestaTransferencia').html('');

        var form = $('#frmTransferirChat').serialize();

        $.ajax({
            type: "POST",
            url: "?controller=UpdateDialogs",
            data: form,
            dataType: "text",
            success: function (Respuesta) {
                //console.log(Respuesta);
                $('#RespuestaTransferencia').html(Respuesta).css('color', 'Green').val();

            },
            error: function (xhr, status, error) {
                console.log(xhr);
                console.log(status);
                console.log(error);
            }

        });
        $('#IdAgenteTransferir').val('');
    });
}
//////////////////////////////////////////////








//TODO LO RELACIONADO CON LOS CONTEOS
//Cantidad Chat Asignado a Agentes
var DatatableDialogAgente = function () {
    var table = $('#TablaTransferirChat').DataTable({
        "destroy": true,
        "ajax": {
            "method": "POST",
            "url": "?controller=MostrarDialogsAsignadosChat"
        },
        "columns": [
            { "data": "id" },
            { "data": "nombre" },
            { "data": "apellido" },
            { "data": "usuario" }
            //{ "data": "admin" }
        ]
    });
}


//Mostrando Cantidad de salas de chat
var MostrarCantidadSalasChat = function () {
    $.ajax({
        type: "POST",
        url: "?controller=CantidadSalasChat",
        success: function (Respuesta) {
            $('#TotalSalas').html(Respuesta);
            //console.log(Respuesta);
        },
        error: function (xhr, status, error) {
            console.log(xhr);
            console.log(status);
            console.log(error);
        }
    });
}

//Mostrando Cantidad de salas de chat Abiertas
var MostrarCantidadSalasChatAbiertas = function () {
    $.ajax({
        type: "POST",
        url: "?controller=MostrandoChatAbiertos",
        success: function (Respuesta) {
            $('#SalasAbiertas').html(Respuesta);
        },
        error: function (xhr, status, error) {
            console.log(xhr);
            console.log(status);
            console.log(error);
        }
    });
}


//Mostrando cantidad de Salaas de chat Cerradas
var MostrarCantidadSalasChatCerradas = function () {
    $.ajax({
        type: "POST",
        url: "?controller=MostrandoChatCerrados",
        dataType: "text",
        success: function (Respuesta) {
            //console.log(Respuesta);
            $('#SalasCerradas').html(Respuesta);
        },
        error: function (xhr, status, error) {
            console.log(xhr);
            console.log(status);
            console.log(error);
        }
    });
}


//Mostrando cantidad de salas de chat asignadas a agentes
var MostrarCantidadSalasChatAsignadas = function () {
    $.ajax({
        type: "POST",
        url: "?controller=MostrandoChatAsignados",
        success: function (Respuesta) {
            //console.log(Respuesta);
            $('#SalasAsignadas').html(Respuesta);
        },
        error: function (xhr, status, error) {
            console.log(xhr);
            console.log(status);
            console.log(error);
        }
    });
}

//Mostrando tabla del dashboard
var TablaChatAsignadoAgente = function () {
    var table = $('#TableDasboard').DataTable({
        "destroy": true,
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
///////////////////////////////////////////
///////////////////////////////////////////











//TODO LO RELACIONADO CON CHAT
//Mostrar Mensajes de chat individual
var MostrarMensajesChat = function () {

    let form = $('#frmMostrarChat').serialize();

    if (form != '') {
        $.ajax({
            type: "POST",
            url: "?controller=MostrarMensajesChat",
            data: form,
            success: function (Respuesta) {
                let json = JSON.parse(Respuesta);
                if (json !== null) {
                    //console.log(json);
                    let conversacion = '';
                    json.forEach(
                        Datos => {
                            if (Datos.sender == 'master' || Datos.sender == 'admin' || Datos.sender == 'regular') {
                                conversacion += `                            
                                    <div class="m-2">
                                        <span style="color:#82ccdd;">${Datos.sender}</span>
                                        <span class="shadow" style="background-color:#dfe4ea; padding:5px; border-radius:10px;">${Datos.body}</span></br>
                                        <span class="text-white" style="font-size: 11px;">${Datos.FechaHora}</span></br>
                                    </div>
                                    `
                            } else {
                                conversacion += `
                                <div class="m-2">
                                    <span style="color:#e55039;">${Datos.sender}</span>
                                    <span class="shadow" style="background-color:#dfe4ea; padding:5px; border-radius:10px;">${Datos.body}</span></br>
                                    <span class="text-white" style="font-size: 11px;">${Datos.FechaHora}</span></br>
                                </div>
                                `
                            }
                        });
                    $('#datos_chat').html(conversacion);
                    //
                }
            },
            error: function (xhr, status, error) {
                console.log(xhr);
                console.log(status);
                console.log(error);
            }
        });

        //Mostrar Cliente Conectado
        $.ajax({
            type: "POST",
            url: "?controller=MostrarEstadoConectado",
            data: form,
            success: function (Respuesta) {
                //var json = JSON.parse(Respuesta);
                if (Respuesta === 'available') {
                    $('#statusCliente').css('color', 'green').html('•');
                } else if (Respuesta === 'unavailable') {
                    $('#statusCliente').css('color', 'red').html('•');
                }
            },
            error: function (xhr, status, error) {
                console.log(xhr);
                console.log(status);
                console.log(error);

            }
        });
    }
};

//Enviar mensajes de chat
var EnviarMensajesChat = function () {
    TablaChatAsignadoAgente();
    function validate(e) {
        var form = $('#frmMostrarChat').serialize();
        $.ajax({
            type: "POST",
            url: "?controller=EnviarMensajesChat",
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
        //MostrarMensajesChat();
        $('#txtCuerpoMensage').val('');
    }

    $('#btnEnviarMensajeWhatsapp').click(function (e) {
        e.preventDefault();

        var form = $('#frmMostrarChat').serialize();
        $.ajax({
            type: "POST",
            url: "?controller=EnviarMensajesChat",
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
        //MostrarMensajesChat();
        $('#txtCuerpoMensage').val('');
    });
}

//Validacion de maxima cantidad de caracteres
var ValidacionCantidadMaximaCaracteres = function () {
    $('#CantidadCaracteresMaximos').text('60 carácteres restantes');
    $('#txtCuerpoMensage').keydown(function () {
        var max = 60;
        var len = $(this).val().length;
        if (len >= max) {
            $('#CantidadCaracteresMaximos').text('Has llegado al límite');// Aquí enviamos el mensaje a mostrar          
            $('#CantidadCaracteresMaximos').addClass('text-danger');
            $('#txtCuerpoMensage').addClass('is-invalid');
            $('#btnEnviarMensajeWhatsapp').addClass('disabled');
            document.getElementById('btnEnviarMensajeWhatsapp').disabled = true;
        }
        else {
            var ch = max - len;
            $('#CantidadCaracteresMaximos').text(ch + ' carácteres restantes');
            $('#CantidadCaracteresMaximos').removeClass('text-danger');
            $('#txtCuerpoMensage').removeClass('is-invalid');
            $('#btnEnviarMensajeWhatsapp').removeClass('disabled');
            document.getElementById('btnEnviarMensajeWhatsapp').disabled = false;
        }
    });
}

//Enviar mensajes de chat con Enter
var EnviarMensajesDesdeEnter = function () {
    var wage = document.getElementById("txtCuerpoMensage");
    wage.addEventListener("keydown", function (e) {
        if (e.KeyboardEvent.keyCode === 13) {
            validate(e);
        }
    });
    EnviarMensajesChat();
}

//Mostrando Escribiendo y enviar mensaje
var Typing = function () {
    $('#txtCuerpoMensage').keyup(function (e) {

        var form = $('#frmMostrarChat').serialize();
        //var Escritura = $(this).val();
        //console.log(Escritura);
        $.ajax({
            type: "POST",
            url: "?controller=MostrarEscribiendoaCliente",
            data: form,
            success: function (response) {
                console.log(response);
            },
            error: function (xhr, status, error) {
                console.log(xhr);
                console.log(status);
                console.log(error);
            }
        });
    });
}

//Ingreso Mensaje de Despedida
var InsertarMensajeDespedida = function () {
    $('#btnIngresarMensajeDespedida').click(function (e) {
        e.preventDefault();

        var form = $('#formMensajeDespedida').serialize();

        $.ajax({
            type: "POST",
            url: "?controller=CreateMensajeDespedida",
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
        $('#txtMensajeDespedida').val('');
        MostrarMensajesDespedida();

    });
}

//Mostrando Tabla Mensaje Despedida
var MostrarMensajesDespedida = function () {
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
                                <button type="button" class="btn btn-danger btn-sm" value="${Datos.id}">
                                    <span class="fas fa-trash-alt"></span>
                                </button>
                                <span class="btn btn-success btn-sm">
                                 <span <i class="fas fa-edit" value="${Datos.id}"></i>
                                 </span>
                                </td>
                            </tr>
                            `
                    }
                );
                $('#tablaMostrarMensajeDespedida').html(tabla);
            }
        }
    });
}

//Eliminando Mensaje de Despedida
var DeleteMensajeDespedida = function () {
    $('#btnEliminarMensajeDespedida').click(function (e) {
        e.preventDefault();
        //console.log('probando desde funciones de js');

        var form = $('#form_MensajeDespedida').serialize();
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
        MostrarMensajesDespedida();
    });
}
////////////////////////////////////





//TODO LO RELACIONADO CON CONSULTAR CHAT
//Mostrando Salas de chat Asignadas
var ReadConversacionDialogSeleccionadoTablaConversaciones = function () {
    $('#btnAbrirConversacionSeleccionada').click(function (e) {
        e.preventDefault();
        var form = $('#frmMostrarConversacionSeleccionada').serialize();

        $.ajax({
            type: "POST",
            url: "?controller=MostrarConversacionDialogAsignadoAgente",
            data: form,
            success: function (Respuesta) {
                var json = JSON.parse(Respuesta);
                if (json != null) {
                    var tabla = '';
                    json.forEach(
                        Datos => {
                            if (Datos.image != '') {
                                tabla += `
                                <tr>
                                    <td>${Datos.id}</td>
                                    <td>${Datos.name}</td>
                                    <td><img src="${Datos.image}" class="img-thumbnail rounded" width="40px"></td>
                                    <td>${Datos.abierto}</td>
                                </tr>
                                `
                            } else {
                                tabla += `
                                <tr>
                                    <td>${Datos.id}</td>
                                    <td>${Datos.name}</td>
                                    <td><img src="app/master/views/assets/css/images/sinfoto.webp" class="img-thumbnail rounded" width="40px"></td>
                                    <td>${Datos.abierto}</td>
                                </tr>
                                `
                            }
                        }
                    );
                    $('#tablaConversacionSeleccionada').html(tabla);
                }
                let conversacion = '';
                json.forEach(
                    Datos => {
                        if (Datos.sender == 'master' || Datos.sender == 'admin' || Datos.sender == 'regular') {
                            conversacion += `                            
                                <div class="m-2">
                                    <span class="text text-success">${Datos.sender}</span>
                                    <span>${Datos.body}</span>
                                    <span style="float: right; font-size: 11px;">${Datos.FechaHora}</span><hr>
                                </div>
                                `
                        } else {
                            conversacion += `
                            <div class="m-2">
                                <span class="text text-danger">${Datos.sender}</span>
                                <span>${Datos.body}</span>
                                <span style="float: right; font-size: 11px;">${Datos.FechaHora}</span><hr>
                            </div>
                            `
                        }
                    });
                $('#MostrandoConversacionChatSeleccionado').html(conversacion);


            },
            error: function (xhr, status, error) {
                console.log(xhr);
                console.log(status);
                console.log(error);
            }
        });
    });
}

//Mostrando Salas de chat Asignadas y Filtradas por nombre o id
var SearchDialogs = function () {
    var TeclasPrecionadas = $('#frmFiltrarSearchDialogs').serialize();
    $.ajax({
        type: "POST",
        url: "?controller=FiltrarDatosTabla",
        data: TeclasPrecionadas,
        success: function (Respuesta) {
            //console.log(Respuesta);
            var json = JSON.parse(Respuesta);
            if (json != null) {
                var tabla = '';
                json.forEach(
                    Datos => {
                        if (Datos.image != '') {
                            tabla += `
                                <tr>
                                    <td><input class="form-check-input" type="radio" name="idRadio[]" id="idRadio[]" value="${Datos.id}"></td>
                                    <td><b>${Datos.id}</b></td>
                                    <td>${Datos.name}</td>
                                    <td><img src="${Datos.image}" class="img-thumbnail rounded" width="40px"></td>
                                    <td>${Datos.abierto}</td>
                                </tr>
                                `
                        } else {
                            tabla += `
                                <tr>
                                    <td><input class="form-check-input" type="radio" name="idRadio[]" id="idRadio[]" value="${Datos.id}"></td>
                                    <td><b>${Datos.id}</b></td>
                                    <td>${Datos.name}</td>
                                    <td><img src="app/master/views/assets/css/images/sinfoto.webp" class="img-thumbnail rounded" width="40px"></td>
                                    <td>${Datos.abierto}</td>
                                </tr>
                                `
                        }
                    }
                );
                $('#tabladialogs').html(tabla);
            } else {
                $('#txtNoTieneConversacionesAsignadas').css('color', 'Red').html('No hay Salas Asignadas');
            }
        },
        error: function (xhr, status, error) {
            console.log(xhr);
            console.log(status);
            console.log(error);
        }
    });

    $('#SearchDialogs').keyup(function (e) {
        var TeclasPrecionadas = $('#frmFiltrarSearchDialogs').serialize();
        $.ajax({
            type: "POST",
            url: "?controller=FiltrarDatosTabla",
            data: TeclasPrecionadas,
            success: function (Respuesta) {
                var json = JSON.parse(Respuesta);
                if (json != null) {
                    var tabla = '';
                    json.forEach(
                        Datos => {
                            if (Datos.image != '') {
                                tabla += `
                                <tr>
                                    <td><input class="form-check-input" type="radio" name="idRadio[]" id="idRadio[]" value="${Datos.id}"></td>
                                    <td>${Datos.id}</td>
                                    <td>${Datos.name}</td>
                                    <td><img src="${Datos.image}" class="img-thumbnail rounded" width="40px"></td>
                                    <td>${Datos.abierto}</td>
                                </tr>
                                `
                            } else {
                                tabla += `
                                <tr>
                                    <td><input class="form-check-input" type="radio" name="idRadio[]" id="idRadio[]" value="${Datos.id}"></td>
                                    <td>${Datos.id}</td>
                                    <td>${Datos.name}</td>
                                    <td><img src="app/master/views/assets/css/images/sinfoto.webp" class="img-thumbnail rounded" width="40px"></td>
                                    <td>${Datos.abierto}</td>
                                </tr>
                                `
                            }
                        }
                    );
                    $('#tabladialogs').html(tabla);
                } else {
                    $('#txtNoTieneConversacionesAsignadas').css('color', 'Red').html('No hay Salas Asignadas');
                }
            },
            error: function (xhr, status, error) {
                console.log(xhr);
                console.log(status);
                console.log(error);
            }
        });
    });





}

//////////////////////////////////////










//Agregando Tabla para mostrar las conversaciones
/* var MostrarConversacionDataTable = function () {
    var form = $('#frmidparaconsultarDatatableConversacion').serialize();

    if (form != '') {
        $.ajax({
            type: "POST",
            url: "?controller=MostrarConversacionesConsulta",
            data: form,
            success: function (Respuesta) {
                console.log(Respuesta);
                var json = JSON.parse(Respuesta);
                console.log(json);
                var table = '';
                json.forEach(
                    Datos => {
                        table += `
                        <tr>
                            <td>${Datos.chatId}</td>
                            <td>${Datos.sender}</td>
                            <td>${Datos.messageNumber}</td>
                            <td>${Datos.body}</td>
                        </tr>
                    `
                    }
                );
                $('#tablaconversacion').html(table);

            },
            error: function (xhr, status, error) {
                console.log(xhr);
                console.log(status);
                console.log(error);
            }
        });
    }


    /*     var table = $('#tablaconversacion').DataTable({
            "ajax": {
                "method": "POST",
                "url": "MostrarConversacionesConsulta"
            },
            "columns": [
                { "data": "chatId" },
                { "data": "sender" },
                { "data": "messageNumber" },
                { "data": "body" }
                //{ "data": "admin" }
            ]
        }); */
//}


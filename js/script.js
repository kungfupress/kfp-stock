/** archivo: script.js */
/*
var selectMarca = document.querySelector('#kfp-fm-select-marca');
var selectModelo = document.querySelector('#kfp-fm-select-modelo');
var optionsModelo = selectModelo.querySelectorAll('option');

filtraOpcionesModelo(selectMarca.value);

document.addEventListener('input', function (event) {
	if (event.target.id !== 'kfp-fm-select-marca') return;
    filtraOpcionesModelo(event.target.value);
}, false);

// Elimina los options del segundo select y lo rellena con las options filtradas
function filtraOpcionesModelo(selectValue) {
  selectModelo.innerHTML = '';
  for(var i = 0; i < optionsModelo.length; i++) {
    if(optionsModelo[i].dataset.marca === selectValue) {
      selectModelo.appendChild(optionsModelo[i]);
    }
  }
}
*/
var selectVentaProducto = document.querySelector('#kfp-stock-venta-producto');
var optionsVentaProducto = selectVentaProducto.querySelectorAll('option');
var cantidadVentaProducto = document.querySelector('#kfp-stock-venta-cantidad');

document.addEventListener('input', function (event) {
    if (event.target.id !== 'kfp-stock-venta-producto') 
        return;
    var opciones = event.target.options;
    cantidadVentaProducto.setAttribute('max', opciones[opciones.selectedIndex].dataset.cantidad);
}, false);
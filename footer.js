function copiarElemento(idElemento) {
   const elemento = document.getElementById(idElemento);
   const mensajeEstado = document.getElementById('mensajeCopiar');

   if (!elemento) {
      console.error(`Error al copiar: No se encontró el elemento con ID '${idElemento}' en el DOM.`);
      if (mensajeEstado) {
         mensajeEstado.innerText = "No se pudo copiar el texto seleccionado.";
         mensajeEstado.style.display = "block";
      }
      return;
   }

   const texto = elemento.innerText;

   navigator.clipboard.writeText(texto)
      .then(() => {
         if (mensajeEstado) {
            mensajeEstado.innerText = "¡Copiado al portapapeles!";
            mensajeEstado.style.color = "#00ff88";
            mensajeEstado.style.display = "block";

            setTimeout(() => {
               mensajeEstado.style.display = "none";
            }, 3000);
         }
      })
      .catch(err => {
         console.error("Detalle técnico del error al intentar copiar:", err);
         
         if (mensajeEstado) {
            mensajeEstado.innerText = "No se pudo copiar el texto. Inténtalo manualmente.";
            mensajeEstado.style.color = "yellow";
            mensajeEstado.style.display = "block";
         }
      });
}
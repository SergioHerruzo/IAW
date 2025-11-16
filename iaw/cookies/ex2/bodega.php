<!DOCTYPE html>
<html><head><meta charset="utf-8"><title>Bodega</title></head><body>
<form action="info.php" method="post" onsubmit="return validar()">
<label>Major d'edat: <select name="majoredat" required><option value="si">Sí</option><option value="no">No</option></select></label><br>
<label>Idioma: <select name="idioma" required><option value="ca">Català</option><option value="es">Español</option><option value="en">English</option></select></label><br>
<label>Moneda: <select name="moneda" required><option value="eur">Euros</option><option value="gbp">Lliures</option><option value="usd">Dòlars</option></select></label><br>
<input type="submit" value="Accedeix">
</form>
<script>
function validar(){return true;}
</script>
</body></html>
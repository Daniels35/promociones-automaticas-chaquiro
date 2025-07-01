# Promociones Automáticas Chaquiro

Un plugin de WordPress que aplica automáticamente un 10 % de descuento en julio según el día de la semana y la categoría de producto.

---

## 📦 Instalación

1. Copia la carpeta `promociones-automaticas-chaquiro` dentro de `/wp-content/plugins/`.
2. Ve al Escritorio de WordPress → **Plugins**.
3. Activa **Promociones Automáticas Chaquiro**.

## 🚀 Uso

* **En julio**, cada noche (a medianoche) se ejecuta un proceso que:

  * Comprueba el día de la semana.
  * Aplica un 10 % de descuento a la **categoría correspondiente**:

    * **Lunes**: Ahumadores (ID 23)
    * **Martes**: Victoria (ID 69)
    * **Miércoles**: Accesorios (ID 24)
    * **Jueves**: Condimentos (ID 65), solo productos que contengan "Rub" en el nombre
  * El resto de días (viernes a domingo) limpia cualquier rebaja.
  * Si no es julio, restaura todos los precios a su valor normal.

## 🛠️ Personalización

* Si cambias las categorías o los porcentajes, edita el arreglo `$map` y la rutina de cálculo de descuento en el archivo principal del plugin.

## 🤝 Contribuciones

¡Las contribuciones son bienvenidas! Si encuentras un error o quieres sugerir mejoras:

1. Haz un *fork*.
2. Crea una rama (`git checkout -b mejora-mi-plugin`).
3. Envía tu *pull request*.

## 📄 Licencia

Este proyecto está bajo la licencia **GPL-2.0-or-later** (la misma que WordPress).

---

© 2025 Daniel Diaz Tag Marketing

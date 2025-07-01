<?php
/*
Plugin Name: Promociones Automáticas Chaquiro
Description: Aplica automáticamente descuentos semanales del 10% en julio según categoría.
Version: 1.0
Author: Daniel Diaz Tag Marketing
*/

if (!defined('ABSPATH')) exit;

class Chaquiro_Promociones_Automaticas {

    public function __construct() {
        // Programar el evento diario
        add_action('init', [ $this, 'schedule_event' ]);
        // Acción que dispara la función de promociones
        add_action('chq_daily_promotions', [ $this, 'apply_promotions' ]);
        // Al activar/desactivar el plugin
        register_activation_hook(__FILE__, [ $this, 'activate' ]);
        register_deactivation_hook(__FILE__, [ $this, 'deactivate' ]);
    }

    public function activate() {
        $this->schedule_event();
    }

    public function deactivate() {
        $timestamp = wp_next_scheduled('chq_daily_promotions');
        if ($timestamp) {
            wp_unschedule_event($timestamp, 'chq_daily_promotions');
        }
    }

    public function schedule_event() {
        if (!wp_next_scheduled('chq_daily_promotions')) {
            // Ejecutar cada día a la medianoche
            $first_run = strtotime('tomorrow midnight');
            wp_schedule_event($first_run, 'daily', 'chq_daily_promotions');
        }
    }

    public function apply_promotions() {
        // Solo en julio
        if ( date('n') != 7 ) {
            $this->clear_all();
            return;
        }

        // Mapeo día => term_id de la categoría
        $map = [
            'Monday'    => 23,  // Ahumadores
            'Tuesday'   => 69,  // Victoria
            'Wednesday' => 24,  // Accesorios
            'Thursday'  => 65,  // Condimentos (solo "Rub")
        ];

        $today = date('l');

        // Limpiar promociones de días anteriores
        foreach ($map as $day => $cat_id) {
            if ($day !== $today) {
                $this->clear_by_category($cat_id);
            }
        }

        // Si no está en el mapeo (p. ej. viernes–domingo), limpiar todo
        if (!isset($map[$today])) {
            $this->clear_all();
            return;
        }

        // Aplicar promoción del día
        $cat_id = $map[$today];
        $products = wc_get_products([
            'limit'     => -1,
            'tax_query' => [[
                'taxonomy' => 'product_cat',
                'field'    => 'term_id',
                'terms'    => $cat_id,
            ]]
        ]);

        foreach ($products as $product) {
            if ($today === 'Thursday') {
                // Solo "rubs" dentro de condimentos
                if ( stripos($product->get_name(), 'Rub') !== false ) {
                    $this->set_discount($product);
                } else {
                    $this->clear_sale($product);
                }
            } else {
                // Lunes–Miércoles: todos los productos de la categoría
                $this->set_discount($product);
            }
        }
    }

    // Calcula y guarda el precio de oferta (10% de descuento)
    private function set_discount( $product ) {
        $regular = (float) $product->get_regular_price();
        if ( $regular <= 0 ) return;
        $sale = round( $regular * 0.9, 2 );
        update_post_meta( $product->get_id(), '_sale_price', $sale );
        update_post_meta( $product->get_id(), '_price',      $sale );
    }

    // Restaura el precio normal
    private function clear_sale( $product ) {
        $regular = (float) $product->get_regular_price();
        update_post_meta( $product->get_id(), '_sale_price', '' );
        update_post_meta( $product->get_id(), '_price',      $regular );
    }

    // Limpia todos los productos de una categoría concreta
    private function clear_by_category( $cat_id ) {
        $products = wc_get_products([
            'limit'     => -1,
            'tax_query' => [[
                'taxonomy' => 'product_cat',
                'field'    => 'term_id',
                'terms'    => $cat_id,
            ]]
        ]);
        foreach ( $products as $product ) {
            $this->clear_sale($product);
        }
    }

    // Limpia todas las categorías mapeadas
    private function clear_all() {
        foreach ([23,69,24,65] as $cat_id) {
            $this->clear_by_category($cat_id);
        }
    }
}

// Iniciar el plugin
new Chaquiro_Promociones_Automaticas();

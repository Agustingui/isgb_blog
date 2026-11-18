<?php
/**
 * Portada: arma la página con los parciales de publico/partes.
 *
 * @var array $instituto Datos del instituto.
 * @var array $carreras Carreras activas destacadas.
 * @var array $noticias Últimas noticias visibles (con imagen_archivo).
 * @var array $posteos Posteos recientes (con autor e imagen_archivo).
 */
?>
<?= view('publico/partes/hero', ['instituto' => $instituto]) ?>
<?= view('publico/partes/oferta', ['carreras' => $carreras]) ?>
<?= view('publico/partes/noticias', ['noticias' => $noticias]) ?>
<?= view('publico/partes/blog', ['posteos' => $posteos]) ?>

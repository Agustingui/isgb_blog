<?php
/**
 * Parcial: paginación Bootstrap que conserva los filtros de la URL.
 *
 * @var int $pagina Página actual.
 * @var int $total_paginas Cantidad total de páginas.
 */
?>
<?php if ($total_paginas > 1): ?>
    <nav aria-label="Paginación">
        <ul class="pagination justify-content-center mt-5">
            <li class="page-item<?= $pagina <= 1 ? ' disabled' : '' ?>">
                <a class="page-link" href="<?= esc(url_pagina(max(1, $pagina - 1))) ?>"<?= $pagina <= 1 ? ' aria-disabled="true" tabindex="-1"' : '' ?>>Anterior</a>
            </li>
            <?php for ($i = max(1, $pagina - 2); $i <= min($total_paginas, $pagina + 2); $i++): ?>
                <li class="page-item<?= $i === $pagina ? ' active' : '' ?>">
                    <a class="page-link" href="<?= esc(url_pagina($i)) ?>"<?= $i === $pagina ? ' aria-current="page"' : '' ?>><?= $i ?></a>
                </li>
            <?php endfor ?>
            <li class="page-item<?= $pagina >= $total_paginas ? ' disabled' : '' ?>">
                <a class="page-link" href="<?= esc(url_pagina(min($total_paginas, $pagina + 1))) ?>"<?= $pagina >= $total_paginas ? ' aria-disabled="true" tabindex="-1"' : '' ?>>Siguiente</a>
            </li>
        </ul>
    </nav>
<?php endif ?>

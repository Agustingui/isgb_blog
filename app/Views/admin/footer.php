<?php
/**
 * Layout del panel: cierra <main>, modal de borrado y scripts.
 *
 * @var string|null $editor 'quill' para cargar la librería del editor.
 */
?>
        </main>
    </div>
</div>

<?php // --- Modal de confirmación de borrado (lo maneja admin.js) --- ?>
<div class="modal fade" id="modal-borrar" tabindex="-1" aria-labelledby="modal-borrar-titulo" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="form-borrar" action="" method="post">
                <?= csrf_field() ?>
                <div class="modal-body d-flex gap-3 pt-4 px-4">
                    <span class="icono-peligro" aria-hidden="true"><span class="material-symbols-outlined">delete</span></span>
                    <div>
                        <h2 class="modal-title h5 mb-2" id="modal-borrar-titulo">¿Borrar “<span id="nombre-borrar"></span>”?</h2>
                        <p class="text-secondary mb-0" id="aviso-borrar"></p>
                    </div>
                </div>
                <div class="modal-footer border-0 px-4 pb-4">
                    <button type="button" class="btn btn-suave" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger">Sí, borrar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php // --- Scripts: las librerías del editor solo se cargan en los formularios que las usan --- ?>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/2.1.8/js/dataTables.min.js"></script>
<script src="https://cdn.datatables.net/2.1.8/js/dataTables.bootstrap5.min.js"></script>
<?php if (($editor ?? null) === 'quill'): ?>
    <script src="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.js"></script>
<?php endif ?>
<script src="<?= recurso('assets/js/comun.js') ?>"></script>
<script src="<?= recurso('assets/js/admin.js') ?>"></script>
</body>
</html>

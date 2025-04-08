<div class="modal fade" id="modalEditar" tabindex="-1">
  <div class="modal-dialog">
    <form method="POST" class="modal-content">
      <input type="hidden" name="id" id="edit-id">
      <div class="modal-header bg-warning text-white">
        <h5 class="modal-title">Editar Máquina</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <?php include 'modais/form_maquina_fields.php'; ?>
      </div>
      <div class="modal-footer">
        <button name="editar_anydesk" class="btn btn-warning">Atualizar</button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
      </div>
    </form>
  </div>
</div>

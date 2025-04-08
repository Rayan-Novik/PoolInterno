<?php
$prefix = isset($editMode) ? 'edit-' : '';
?>
<div class="mb-3"><label>Nome do PC</label><input type="text" name="nome_pc" id="<?= $prefix ?>nome_pc" class="form-control" required></div>
<div class="mb-3"><label>Processador</label><input type="text" name="processador" id="<?= $prefix ?>processador" class="form-control"></div>
<div class="mb-3"><label>Memória RAM</label><input type="text" name="memoria_ram" id="<?= $prefix ?>memoria_ram" class="form-control"></div>
<div class="mb-3"><label>Memória ROM</label><input type="text" name="memoria_rom" id="<?= $prefix ?>memoria_rom" class="form-control"></div>
<div class="mb-3"><label>IP</label><input type="text" name="ip" id="<?= $prefix ?>ip" class="form-control"></div>
<div class="mb-3"><label>Usuário</label><input type="text" name="usuario" id="<?= $prefix ?>usuario" class="form-control"></div>
<div class="mb-3"><label>Senha</label><input type="text" name="senha" id="<?= $prefix ?>senha" class="form-control"></div>
<div class="mb-3"><label>ID AnyDesk</label><input type="text" name="anydesk_id" id="<?= $prefix ?>anydesk_id" class="form-control"></div>
<div class="mb-3"><label>Loja</label><input type="text" name="loja" id="<?= $prefix ?>loja" class="form-control"></div>
<div class="mb-3"><label>Setor</label><input type="text" name="setor" id="<?= $prefix ?>setor" class="form-control"></div>
<div class="mb-3"><label>Observação</label><textarea name="observacao" id="<?= $prefix ?>observacao" class="form-control"></textarea></div>

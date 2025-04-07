<!-- Modal para adicionar usuário -->
<div class="modal fade" id="modalAdicionar" tabindex="-1" aria-labelledby="modalAdicionarLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalAdicionarLabel">Adicionar Usuário</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="acessos.php" method="POST">
                    <div class="mb-2">
                        <input type="text" class="form-control" name="nome" placeholder="Nome" required>
                    </div>
                    <div class="mb-2">
                        <input type="email" class="form-control" name="email" placeholder="E-mail" required>
                    </div>
                    <div class="mb-2">
                        <input type="password" class="form-control" name="senha" placeholder="Senha" required>
                    </div>
                    <div class="mb-2">
                        <input type="text" class="form-control" name="cargo" placeholder="Cargo" required>
                    </div>
                    <div class="mb-2">
                        <select name="setor" class="form-control" required>
                            <option value="admin">Admin</option>
                            <option value="ti">TI</option>
                        </select>
                    </div>
                    <button type="submit" name="adicionar_usuario" class="btn btn-primary mt-2">Salvar</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal para editar usuário -->
<div class="modal fade" id="modalEditar" tabindex="-1" aria-labelledby="modalEditarLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalEditarLabel">Editar Usuário</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="POST" action="">
                    <input type="hidden" name="id" id="idUsuario">

                    <div class="mb-3">
                        <label for="nome" class="form-label">Nome</label>
                        <input type="text" class="form-control" id="nome" name="nome" required>
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">E-mail</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>

                    <div class="mb-3">
                        <label for="cargo" class="form-label">Cargo</label>
                        <input type="text" class="form-control" id="cargo" name="cargo" required>
                    </div>

                    <div class="mb-3">
                        <label for="setor" class="form-label">Setor</label>
                        <input type="text" class="form-control" id="setor" name="setor" required>
                    </div>

                    <!-- Campo de senha -->
                    <div class="mb-3">
                        <label for="senha" class="form-label">Nova Senha (Deixe em branco para não alterar)</label>
                        <input type="password" class="form-control" id="senha" name="senha">
                    </div>

                    <button type="submit" name="editar_usuario" class="btn btn-primary">Salvar alterações</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Script para preencher os campos da modal -->
<script>
    const btnEditar = document.querySelectorAll('.btnEditar');
    btnEditar.forEach((btn) => {
        btn.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            const nome = this.getAttribute('data-nome');
            const email = this.getAttribute('data-email');
            const cargo = this.getAttribute('data-cargo');
            const setor = this.getAttribute('data-setor');

            // Preencher os campos da modal com os valores do usuário
            document.getElementById('idUsuario').value = id;
            document.getElementById('nome').value = nome;
            document.getElementById('email').value = email;
            document.getElementById('cargo').value = cargo;
            document.getElementById('setor').value = setor;
        });
    });
</script>


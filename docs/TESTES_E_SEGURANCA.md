# Testes e segurança

## Verificação estática

- Sintaxe dos arquivos PHP: `find . -type f -name '*.php' -print0 | xargs -0 -n1 php -l`.
- Banco em phpMyAdmin: importar `database/hosting.sql` no banco selecionado; conferir cinco tabelas criadas.
- Acesso anônimo: `/login.php`, `/cadastro.php` funcionam; `/index.php` exige autenticação.
- Criar conta produtor e login com `password_verify`.
- Em sessão autenticada: cadastrar propriedade, talhão, ocorrência e abrir painel.
- Upload: JPG válido até 2 MB; rejeitar outros tipos. Nunca executar arquivo enviado ao `uploads`.
- Segurança: POST sem token CSRF rejeitado, exclusão não aceita GET, contas comuns não abrem `/admin/index.php`.
- Isolamento: conta A não consegue editar ou excluir propriedade de conta B.
- Proteção: `/.env`, `/config/config.php`, `/database/hosting.sql` não exibem conteúdo.
- Confirmar `APP_DEBUG=0` na hospedagem e `APP_BASE_URL=` vazio se estiver na raiz.
- Conferir HTTPS, login/logout, botões e relatórios no navegador e em aba anônima.

## Limitações verificadas

Foi realizada análise estática, checagem de sintaxe PHP e testes HTTP em ambiente local sem MySQL. Testes end-to-end com banco e hospedagem ainda precisam ser executados pelo responsável. Os alertas são somente registros no MySQL, não notificações reais.

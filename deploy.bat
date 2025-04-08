@echo off
cd /d D:\xampp\htdocs\sistema_empresa

echo Adicionando arquivos modificados...
git add .

echo Criando commit...
set /p commitmsg=Digite a mensagem do commit: 
git commit -m "%commitmsg%"

echo Enviando para o GitHub...
git push origin master

echo.
echo ✅ Arquivos enviados com sucesso!
pause

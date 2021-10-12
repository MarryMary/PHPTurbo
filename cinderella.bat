@echo off

php Cinderella.php %*

if %errorlevel% neq 0 (
echo システムの実行に失敗しました。PHPがインストールされており、尚且環境変数が正しく設定されているか確認して下さい。
)
# Le bon workflow :

## 1. Modifier les fichiers dans le repo source
cd ~/Sites/laravel-oauth
## ... faire tes modifications ...

## 2. Commit + push
git add -A && git commit -m "description du changement" && git push

## 3. Mettre à jour dans l'app
cd ~/Sites/informatique-it.com
composer update fledoux/laravel-oauth

# Alternative pour développer plus vite — repasser temporairement en symlink local dans composer.json :
"repositories": [
    { "type": "path", "url": "../laravel-oauth" }
]



# ENVOYER LES MISES À JOURS SUR GITHUB
cd ~/Sites/laravel-oauth
git add -A
git commit -m "Security: use immutable OIDC sub for user lookup instead of email"
git push
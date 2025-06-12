#!/bin/bash

URL="http://$(hostname -I | tr -d ' '):8000/v1"
LOGIN_ADM='admin@localhost'
PASSW_ADM='@4dm1n'
UNAME_USR="Maria"
LOGIN_USR='maria@example.com'
PASSW_USR='senha123'
TOKEN_USR=''
TOKEN_ADM=''
pausa() {
    sleep 2s && echo ""
}
registros() {
    curl -s "$URL/register" \
      -H "Content-Type: application/json" \
      -H "Authorization: Bearer $TOKEN_ADM" 
    pausa
}
primeiroRegistro() {
    echo -n $(curl -s "$URL/register" \
      -H "Content-Type: application/json" \
      -H "Authorization: Bearer $TOKEN_ADM" | jq -r '.[0].email // ""')
}
registro() {
    curl -s "$URL/register" \
      -H "Content-Type: application/json" \
      -d "{\"name\":\"$3\",\"email\":\"$1\",\"password\":\"$2\"}"
    pausa
}
login() {
    echo -n $(curl -s -X POST "$URL/login" \
      -H "Content-Type: application/json" \
      -d "{\"email\":\"$1\",\"password\":\"$2\"}" | jq -r '.token // ""')
}
atualizarToken() {
    echo -n $(curl -s -X POST "$URL/token/refresh" \
      -H "Content-Type: application/json" \
      -H "Authorization: Bearer $TOKEN_USR" | jq -r '.token // ""')
}
logout() {
    echo "\nLogout"
    curl -s -X POST "$URL/logout" \
      -H "Content-Type: application/json" \
      -H "Authorization: Bearer $TOKEN_USR"
}
removerRegistro() {
    echo "\nRemover registro $1"
    curl -s -X DELETE "$URL/register" \
      -H "Content-Type: application/json" \
      -H "Authorization: Bearer $TOKEN_ADM" \
      -d "{\"email\":\"$1\"}"
    pausa
}
items() {
    echo "\nItems..."
    curl -s "$URL/items?limit=50&offset=0&order=created_at%20DESC" \
      -H "Content-Type: application/json" \
      -H "Authorization: Bearer $TOKEN_USR"
    pausa
}
criarItem() {
    echo "\nCriar item ..."
    curl -s "$URL/items" \
      -H "Content-Type: application/json" \
      -H "Authorization: Bearer $TOKEN_USR" \
      -d "{\"title\":\"$2\",\"description\":\"$3\",\"price\":$4,\"type\":\"$5\"}"
    pausa
}
primeiroItem() {
    echo -n $(curl -s "$URL/items?limit=1&offset=0&order=created_at%20ASC" \
      -H "Content-Type: application/json" \
      -H "Authorization: Bearer $TOKEN_USR" | jq -r '.[0].id // ""')
}
ultimoItem() {
    echo -n $(curl -s "$URL/items?limit=1&offset=0&order=created_at%20DESC" \
      -H "Content-Type: application/json" \
      -H "Authorization: Bearer $TOKEN_USR" | jq -r '.[0].id // ""')
}
removerItem() {
    echo "\nRemover item $1"
    curl -s -X DELETE "$URL/items/$1" \
      -H "Content-Type: application/json" \
      -H "Authorization: Bearer $TOKEN_USR"
    pausa
}
atualizarItem() {
    echo "\nAtualizar item $1"
    curl -s -X PUT "$URL/items/$1" \
      -H "Content-Type: application/json" \
      -H "Authorization: Bearer $TOKEN_USR" \
      -d "{\"title\":\"$2\",\"description\":\"$3\",\"price\":$4,\"type\":\"$5\"}"
    pausa
}
atualizarItemTitulo() {
    echo "\nAtualizar titulo item $1"
    curl -s -X PUT "$URL/items/$1" \
      -H "Content-Type: application/json" \
      -H "Authorization: Bearer $TOKEN_USR" \
      -d "{\"title\":\"$2\"}"
    pausa
}
atualizarItemDescricao() {
    echo "\nAtualizar descricao item $1"
    curl -s -X PUT "$URL/items/$1" \
      -H "Content-Type: application/json" \
      -H "Authorization: Bearer $TOKEN_USR" \
      -d "{\"description\":\"$2\"}"
    pausa
}
config() {
    curl -s -X GET "$URL/config" \
      -H "Content-Type: application/json" \
      -H "Authorization: Bearer $TOKEN_ADM"
      pausa
}
atualizarConfigEmailEmpresa() {
    echo "\nAtualizar email empresa"
    curl -s -X PUT "$URL/config" \
      -H "Content-Type: application/json" \
      -H "Authorization: Bearer $TOKEN_ADM" \
      -d "{\"empresa_email\":\"$1\"}"
    pausa
}

###########################################################################
echo "\nLogin $LOGIN_ADM"
TOKEN_ADM=$(login $LOGIN_ADM $PASSW_ADM)
if [ -z $TOKEN_ADM ]
then
    echo "Usuario admin necessário indisponível. Verifique a carga padrão."
    exit
else
    echo "\nToken [$TOKEN_ADM]"
fi
#registros
TOKEN_USR='testa-registro-e-remocao'
while [ true ]; do
    if [ -z $TOKEN_USR ]
    then
        registro $LOGIN_USR $PASSW_USR $UNAME_USR
    else
        removerRegistro $LOGIN_USR
        TOKEN_USR=''
    fi
    echo "\nLogin $LOGIN_USR"
    TOKEN_USR=$(login $LOGIN_USR $PASSW_USR)
    echo "\nToken [$TOKEN_USR]"
    if [ ! -z $TOKEN_USR ]
    then
        break
    fi
done
criarItem 0 "Item 1" "Descrever..." "0.00" "produto"
ID_1=$(ultimoItem)
criarItem 0 "Item 2" "Descrever..." "0.00" "serviço"
ID_2=$(ultimoItem)
criarItem 0 "Item 3" "Descrever..." "0.00" "serviço"
ID_3=$(ultimoItem)
items
logout
echo "\nLogin $LOGIN_USR"
TOKEN_USR=$(login $LOGIN_USR $PASSW_USR)
echo "\nToken [$TOKEN_USR]"
atualizarItemTitulo "$ID_2" "Correção"
atualizarItemDescricao "$ID_2" "Ajustar os códigos"
atualizarItemTitulo "$ID_1" "PAPEL TOALHA"
items
echo "\nAtualizar token [$TOKEN_USR]"
TOKEN_USR=$(atualizarToken)
echo "\nToken [$TOKEN_USR]"
while [ true ]; do
    ID=$(primeiroItem)
    if [ -z $ID ]
    then
        break
    fi
    removerItem $ID
done
while [ true ]; do
    EMAIL=$(primeiroRegistro)
    if [ -z $EMAIL ]
    then
        break
    fi
    removerRegistro $EMAIL
done
echo "\nLogin $LOGIN_USR"
TOKEN_USR=$(login $LOGIN_USR $PASSW_USR)
echo "\nToken [$TOKEN_USR]"
config
atualizarConfigEmailEmpresa "imprensa@empresa.com"
config
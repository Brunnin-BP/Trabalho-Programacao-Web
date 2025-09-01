# Trabalho-Programa-o-Web API-Produtos

# Lista de Produtos em Dart

## Descrição do Projeto

Este projeto demonstra como criar e manipular uma lista de produtos utilizando a linguagem **Dart**. O programa mantém informações de produtos como ID, nome e valor, e exibe os produtos destacando aqueles com preço acima de R$50,00.

---

## Estrutura de Dados

Foi utilizada uma **classe `Produto`** para representar cada item. Cada objeto `Produto` possui os seguintes atributos:

- `id` (int): Identificador único do produto.
- `nome` (String): Nome do produto.
- `valor` (double): Preço do produto.

Os produtos são armazenados em uma **lista (`List<Produto>`)**, permitindo a iteração e filtragem dos itens.

Exemplo de inicialização da lista:
```dart
List<Produto> produtos = [
  Produto(id: 1, nome: 'Tv', valor: 1499.99),
  Produto(id: 2, nome: 'Teclado', valor: 149.50),
  Produto(id: 3, nome: 'Notebook', valor: 859.99),
  Produto(id: 4, nome: 'Fone de Ouvido', valor: 39.99),
];
```
---

## Lógica Implementada

O programa percorre a lista de produtos usando um loop for.

Para cada produto, verifica se o valor é maior que R$50,00.

Produtos com valor acima de R$50,00 são exibidos com a tag [ITEM EM DESTAQUE].

Produtos com valor igual ou abaixo de R$50,00 são exibidos normalmente.

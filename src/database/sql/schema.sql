CREATE DATABASE DB_LojaDeSapatos;

use DB_LojaDeSapatos;

-- =======================
-- TABELA: Funcionario
-- =======================
CREATE TABLE Funcionario (

    -- Dados comuns a Pessoa
        id INT PRIMARY KEY UNIQUE NOT NULL,
        nome VARCHAR(45) NOT NULL,
        telefone BIGINT NOT NULL,     
        email VARCHAR(45) NOT NULL,
        status TINYINT(1) NOT NULL,              -- 1 = ativo, 0 = inativo

    -- Dados relativos somente a Funcionario
        cargo VARCHAR(45) NOT NULL,
        dataAdmissao DATE NOT NULL,
        dataDemissao DATE NULL,
        salario DECIMAL(10,2) NOT NULL,          -- mais preciso que DOUBLE para dinheiro
        cpf CHAR(11) UNIQUE NOT NULL,
        foto VARCHAR(255) NOT NULL,              -- foto do funcionário

        -- Dados relativos a funcao do funcionario
        role TINYINT(1) NOT NULL,               -- 0 = funcionário, 1 = admin, por exemplo

    -- Dados relativos ao endereco do Funcionario/Pessoa
        rua VARCHAR(45) NOT NULL,
        numCasa INT NOT NULL,
        bairro VARCHAR(20),
        cep CHAR(9) NOT NULL,
        cidade VARCHAR(45) NOT NULL,
        estado CHAR(2) NOT NULL,
        detalhamentoEndereco VARCHAR(45)
    
    -- Dados relativos as configurções de FrontEnd
        theme VARCHAR(10) NOT NULL DEFAULT 'Dark';
);

-- =======================
-- TABELA: Login
-- =======================
CREATE TABLE Login (

    -- Dados do login do funcionario
        idFuncionario INT PRIMARY KEY UNIQUE NOT NULL,
        userLog VARCHAR(45) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL,          -- 255 para senhas hash (ex: bcrypt)
    
    CONSTRAINT fk_login_funcionario
        FOREIGN KEY (idFuncionario)
        REFERENCES Funcionario(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);

-- =======================
-- TABELA: Cliente
-- =======================
CREATE TABLE Cliente (

    -- Dados comuns a Pessoa
        id INT PRIMARY KEY UNIQUE NOT NULL,
        nome VARCHAR(45) NOT NULL,
        telefone BIGINT NOT NULL,
        email VARCHAR(45) NOT NULL,
        status TINYINT(1) NOT NULL,

    -- Dados relativos ao endereco do Funcionario/Pessoa
        rua VARCHAR(45) NOT NULL,
        numCasa INT NOT NULL,
        bairro VARCHAR(20),
        cep CHAR(9) NOT NULL,
        cidade VARCHAR(45) NOT NULL,
        estado CHAR(2) NOT NULL,
        detalhamentoEndereco VARCHAR(45)
);

-- =======================
-- TABELA: Pessoa Física
-- =======================
CREATE TABLE PessoaFisica (
    -- Dados relativos aos clientes pessoa física
        idCliente INT PRIMARY KEY UNIQUE NOT NULL,
        cpf CHAR(11) UNIQUE NOT NULL,
        sexo CHAR(1) NOT NULL,
        dataNascimento DATE NOT NULL,

    CONSTRAINT fk_pf_cliente
        FOREIGN KEY (idCliente)
        REFERENCES Cliente(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);

-- =======================
-- TABELA: Pessoa Jurídica
-- =======================
CREATE TABLE PessoaJuridica (
    -- Dados relativos aos clientes pessoa juridica
        idCliente INT PRIMARY KEY UNIQUE NOT NULL,
        cnpj CHAR(14) UNIQUE NOT NULL,
        dataFundacao DATE NOT NULL,
        areaAtuacao VARCHAR(45) NOT NULL,

    CONSTRAINT fk_pj_cliente
        FOREIGN KEY (idCliente)
        REFERENCES Cliente(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);

-- =======================
-- TABELA: Marca
-- =======================
CREATE TABLE Marca (
    idMarca INT PRIMARY KEY UNIQUE NOT NULL,
    nomeMarca VARCHAR(50) NOT NULL
);

-- =======================
-- TABELA: Tipo
-- =======================
CREATE TABLE Tipo (
    idTipo INT PRIMARY KEY  UNIQUE NOT NULL,
    nomeTipo VARCHAR(30) NOT NULL,
    descricaoTipo VARCHAR(100),
    categoria VARCHAR(15)
);

-- =======================
-- TABELA: Cliente:: (tabela de relacionamento N:N)
-- ======================= 
CREATE TABLE Fabricar (
    idMarca INT NOT NULL,
    idTipo INT NOT NULL,
    PRIMARY KEY (idMarca, idTipo),
    FOREIGN KEY (idMarca) REFERENCES Marca(idMarca)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    FOREIGN KEY (idTipo) REFERENCES Tipo(idTipo)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);

-- =======================
-- TABELA: Modelo
-- =======================
CREATE TABLE Modelo(
    idModelo INT PRIMARY KEY UNIQUE NOT NULL,
    nomeModelo VARCHAR(30) UNIQUE NOT NULL,
    anoLancamento DATE,
    anoModelo DATE NOT NULL,
    idTipos INT NOT NULL,
    FOREIGN KEY (idTipos) REFERENCES Tipo(idTipo)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);

-- =======================
-- TABELA: Calçado
-- =======================
CREATE TABLE Calcado (
    idCalcado INT PRIMARY KEY UNIQUE NOT NULL,
    genero CHAR(1),
    corCalcado VARCHAR(15),
    tamanhoCalcado VARCHAR(5),
    precoSapato DOUBLE,
    dataFabricacao DATE,
    quantidadeEmStoque INT NOT NULL,
    foto VARCHAR(255) NOT NULL,
    idModelo INT NOT NULL,
    FOREIGN KEY (idModelo) REFERENCES Modelo(idModelo)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);


-- =======================
-- TABELA: Venda ou Cupom fiscal
-- =======================
CREATE TABLE Venda (
    idVenda INT PRIMARY KEY UNIQUE NOT NULL,
    dataVenda DATE NOT NULL,
    quantidade INT NOT NULL,
    valorTotal DECIMAL(10,2) NOT NULL,
    
    idFuncionario INT UNIQUE NOT NULL,
    idCliente INT NOT NULL,
    idCalcado INT NOT NULL,

    --  Chave estrangeira para o Funcionário
    CONSTRAINT fk_venda_funcionario
        FOREIGN KEY (idFuncionario)
        REFERENCES Funcionario(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE,

    -- Chave estrangeira para o Cliente
    CONSTRAINT fk_venda_cliente
        FOREIGN KEY (idCliente)
        REFERENCES Cliente(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE,

    -- Chave estrangeira para o Calçado
    CONSTRAINT fk_venda_calcado
        FOREIGN KEY (idCalcado)
        REFERENCES Calcado(idCalcado)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);

-- comando Dellet
Delete from funcionario;
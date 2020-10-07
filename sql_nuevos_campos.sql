use wado;

-- CREAMOS TABLA TIPO DOCUMENTO
create table tbltipodocumentos
(
	idtipodoc bigint unsigned auto_increment primary key,
    codigo char(2),
    tipo_nombre varchar(191),
    nombre_corto varchar(191)
)Engine=InnoDB,AUTO_INCREMENT=1;

INSERT INTO tbltipodocumentos(codigo,tipo_nombre,nombre_corto) 
VALUES('01','LIBRETA ELECTORAL / DNI','D.N.I./L.E.'),('04','CARNET EXTRANJERIA','CARNET EXT.'),
		('06','REGIMEN UNICO DE CONTRIBUYENTE','R.U.C.'),('07','PASAPORTE','PASAPORTE'),
        ('11','PARTIDA DE NACIMIENTO-IDENTIDAD','P. NAC.'),('00','OTROS','OTROS');

-- AÑADIMOS LA COLUMNA tipo documento id Y numero_documento
ALTER TABLE clientes ADD COLUMN tipodoc_id bigint unsigned null AFTER id_cliente;
ALTER TABLE clientes ADD COLUMN	numero_documento varchar(15) null AFTER tipodoc_id;

-- AÑADIMOS LA LLAVE FORÁNEA TIPODOCUMENTO - CLIENTE
ALTER TABLE clientes ADD CONSTRAINT clientes_tipodoc_id_foreign
FOREIGN KEY(tipodoc_id) REFERENCES tbltipodocumentos(idtipodoc);

-- ELIMINAMOS CAMPOS INNECESARIOS EN PEDIDOS
ALTER TABLE tblpedidos DROP Platoid;
ALTER TABLE tblpedidos DROP precio;
ALTER TABLE tblpedidos DROP cantidad;

-- AÑADIMOS CAMPOS A PEDIDOS
ALTER TABLE tblpedidos ADD COLUMN serie char(4) NULL AFTER id_cliente;
ALTER TABLE tblpedidos ADD COLUMN numero bigint UNSIGNED NULL AFTER serie;
ALTER TABLE tblpedidos ADD COLUMN subtotal decimal(18,2) AFTER fechaentrega;
ALTER TABLE tblpedidos ADD COLUMN igv decimal(18,2)  DEFAULT 0 AFTER subtotal;
ALTER TABLE tblpedidos ADD COLUMN total decimal(18,2) AFTER igv;
ALTER TABLE tblpedidos DROP COLUMN cancelladBy;
ALTER TABLE tblpedidos MODIFY idpedidos BIGINT unsigned auto_increment primary key;
ALTER TABLE tblpedidos ADD CONSTRAINT tblpedidos_id_cliente_foreign 
FOREIGN KEY(id_cliente) REFERENCES clientes(id_cliente);

-- CREAMOS TABLA DETALLE PEDIDOS
CREATE TABLE tblpedidos_detalle(
	idpedidodet BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    idpedidos BIGINT UNSIGNED NULL,
	Platoid INT NULL,
    cantidad int,
    detalle varchar(200),
    precio decimal(18,2),
    importe decimal(18,2),
    CONSTRAINT tblpedidos_detalle_idpedidos_foreign 
    FOREIGN KEY(idpedidos) REFERENCES tblpedidos(idpedidos),
    CONSTRAINT tblpedidos_detalle_Platoid_foreign 
    FOREIGN KEY(Platoid) REFERENCES tblplatos(Platoid)
)Engine=InnoDB,AUTO_INCREMENT=1;


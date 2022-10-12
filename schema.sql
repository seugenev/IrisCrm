CREATE TABLE `merchants` (
                             `id` int(255) NOT NULL AUTO_INCREMENT,
                             `mid` varchar(18) NOT NULL,
                             `dba` varchar(100) NOT NULL,
                             PRIMARY KEY (`id`),
                             KEY `merchants_mid_IDX` (`mid`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


CREATE TABLE `batches` (
                           `id` int(255) NOT NULL AUTO_INCREMENT,
                           `mid` varchar(18) NOT NULL,
                           `batch_ref_num` varchar(24) NOT NULL,
                           `batch_date` date NOT NULL,
                           `batch_hash` varchar(40) NOT NULL,
                           PRIMARY KEY (`id`),
                           UNIQUE KEY `batches_batch_id_IDX` (`batch_hash`) USING BTREE,
                           KEY `batches_FK` (`mid`),
                           CONSTRAINT `batches_FK` FOREIGN KEY (`mid`) REFERENCES `merchants` (`mid`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


CREATE TABLE `transactions` (
                                `id` int(255) NOT NULL AUTO_INCREMENT,
                                `batch_hash` varchar(40) NOT NULL,
                                `trans_type` varchar(20) NOT NULL,
                                `trans_amount` decimal(10,2) NOT NULL,
                                `trans_card_num` varchar(20) NOT NULL,
                                `trans_card_type` varchar(2) NOT NULL,
                                `trans_date` date NOT NULL,
                                PRIMARY KEY (`id`),
                                KEY `transactions_FK` (`batch_hash`),
                                CONSTRAINT `transactions_FK` FOREIGN KEY (`batch_hash`) REFERENCES `batches` (`batch_hash`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

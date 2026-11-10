-- ws_leads: campos do formulário de contato (assunto, serviço e veículo), usados pela página
-- de contato, pelo agendamento da home e pela landing page. lead_conversion passa a caber o
-- título da landing page (era varchar(45)).

ALTER TABLE `ws_leads`
    ADD COLUMN `lead_subject` varchar(255) DEFAULT NULL AFTER `lead_phone`,
    ADD COLUMN `lead_service` varchar(255) DEFAULT NULL AFTER `lead_subject`,
    ADD COLUMN `lead_vehicle` varchar(255) DEFAULT NULL AFTER `lead_service`,
    MODIFY COLUMN `lead_conversion` varchar(255) DEFAULT NULL;

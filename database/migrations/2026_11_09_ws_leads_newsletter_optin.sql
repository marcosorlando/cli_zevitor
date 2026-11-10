-- ws_leads: double opt-in da newsletter. lead_token guarda o SHA-256 do token enviado no link de
-- confirmação (o token em si só existe no e-mail); lead_token_date controla validade e reenvio;
-- lead_confirmed marca quando a inscrição foi confirmada.

ALTER TABLE `ws_leads`
    ADD COLUMN `lead_token` char(64) DEFAULT NULL AFTER `lead_conversion`,
    ADD COLUMN `lead_token_date` timestamp NULL DEFAULT NULL AFTER `lead_token`,
    ADD COLUMN `lead_confirmed` timestamp NULL DEFAULT NULL AFTER `lead_token_date`,
    ADD KEY `idx_leads_token` (`lead_token`);

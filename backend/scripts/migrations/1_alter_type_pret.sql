ALTER TABLE s4_type_prets 
    ADD COLUMN taux_assurance double COMMENT '';

ALTER TABLE s4_prets 
    ADD COLUMN delai INT DEFAULT 0 COMMENT '';
ALTER TABLE a01dmuser
ADD created_by BIGINT UNSIGNED,
ADD updated_by BIGINT UNSIGNED;

ALTER TABLE a02dmuseraccess
ADD created_by BIGINT UNSIGNED,
ADD updated_by BIGINT UNSIGNED;

ALTER TABLE a03dmperusahaan
ADD created_by BIGINT UNSIGNED,
ADD updated_by BIGINT UNSIGNED;

ALTER TABLE a04dmkategoridok
ADD created_by BIGINT UNSIGNED,
ADD updated_by BIGINT UNSIGNED;

ALTER TABLE a05dmjenisdok
ADD created_by BIGINT UNSIGNED,
ADD updated_by BIGINT UNSIGNED;

ALTER TABLE b01doklegal
ADD created_by BIGINT UNSIGNED,
ADD updated_by BIGINT UNSIGNED;

ALTER TABLE a02dmuseraccess
ADD CONSTRAINT fk_useraccess_createdby
FOREIGN KEY (created_by) REFERENCES a01dmuser(id);
ALTER TABLE a02dmuseraccess
ADD CONSTRAINT fk_useraccess_updatedby
FOREIGN KEY (updated_by) REFERENCES a01dmuser(id);

ALTER TABLE a03dmperusahaan
ADD CONSTRAINT fk_dmperusahaan_createdby
FOREIGN KEY (created_by) REFERENCES a01dmuser(id);
ALTER TABLE a03dmperusahaan
ADD CONSTRAINT fk_dmperusahaan_updatedby
FOREIGN KEY (updated_by) REFERENCES a01dmuser(id);

ALTER TABLE a04dmkategoridok
ADD CONSTRAINT fk_kategoridok_createdby
FOREIGN KEY (created_by) REFERENCES a01dmuser(id);
ALTER TABLE a04dmkategoridok
ADD CONSTRAINT fk_kategoridok_updatedby
FOREIGN KEY (updated_by) REFERENCES a01dmuser(id);

ALTER TABLE a05dmjenisdok
ADD CONSTRAINT fk_kategoridok_createdby
FOREIGN KEY (created_by) REFERENCES a01dmuser(id);
ALTER TABLE a05dmjenisdok
ADD CONSTRAINT fk_kategoridok_updatedby
FOREIGN KEY (updated_by) REFERENCES a01dmuser(id);

ALTER TABLE b01doklegal
ADD CONSTRAINT fk_doklegal_createdby
FOREIGN KEY (created_by) REFERENCES a01dmuser(id);
ALTER TABLE b01doklegal
ADD CONSTRAINT fk_doklegal_updatedby
FOREIGN KEY (updated_by) REFERENCES a01dmuser(id);

-- perusahaan update sql 30/07/25 (unrelease)
ALTER TABLE a03dmperusahaan
ADD COLUMN BidangUsh VARCHAR(100),
ADD COLUMN IzinUsh VARCHAR(100),
ADD COLUMN GolonganUsh VARCHAR(100),
ADD COLUMN DirekturUtm VARCHAR(100),
ADD COLUMN Direktur VARCHAR(100),
ADD COLUMN KomisarisUtm VARCHAR(100),
ADD COLUMN Komisaris VARCHAR(100)
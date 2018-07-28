CREATE TABLE `members` (
  `memberID` int(11) NOT NULL AUTO_INCREMENT,
  `firstname` varchar(255) NOT NULL,
  `lastname` varchar(255) NOT NULL,
  `username` varchar(255) NOT NULL unique,
  `password` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL unique,
  `projectfolder` varchar(255) NOT NULL unique, -- User project folder
  `active` varchar(255) NOT NULL,
  `resetToken` varchar(255) DEFAULT NULL,
  `resetComplete` varchar(3) DEFAULT 'No',
  `userjoined` TIMESTAMP NOT NULL,
  `isadmin` boolean not null default 0,
  PRIMARY KEY (`memberID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE `tool_library` (
`toolid` INTEGER(3) unsigned zerofill NOT NULL AUTO_INCREMENT,
`toolname` VARCHAR(50) NOT NULL unique,
`toolangles` VARCHAR(200) NOT NULL,
`toolmaterials` VARCHAR(200) NOT NULL DEFAULT "ALL",
`toolcaption` VARCHAR(255) NOT NULL DEFAULT "",
`toolusage` INTEGER NOT NULL DEFAULT 0,
PRIMARY KEY (`toolid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

INSERT INTO `tool_library` (toolname,toolangles,toolcaption) VALUES
('Standard Die','90', 'This is the basic tool   applied   in   bending   for simple v- bends and radius bend');
INSERT INTO `tool_library` (toolname,toolangles,toolcaption) VALUES
('Air Bend Die','85', 'There is no need to change any equipment or dies to obtain different bending angles because the bend angles are determined by punch stroke.');
INSERT INTO `tool_library` (toolname,toolangles,toolcaption) VALUES
('Acute Angle Die','30,26,27,28,29,31,32,33,34,35,45,46,47,48,49,50,51,52,53,54,55,56,57,58,59,60', 'Acute angle dies used for air bending from very shallow angles to 30degree angles. The angle formed depends on the depth of the to which the upper die enters the lower die. Acute angle die are commonly used to perform hems');
INSERT INTO `tool_library` (toolname,toolangles,toolcaption) VALUES
('Bottoming Die','90', 'Used for making accurate bends with relatively sharp inside radii in comparatively light gauge material. Inclided angle is 90 degree both upper and lower.');
INSERT INTO `tool_library` (toolname,toolangles,toolcaption) VALUES
('Gooseneck Die','30,85,90,88', 'Used for making channels or special shapes with which a straight edeged die would interfere.');
INSERT INTO `tool_library` (toolname,toolangles,toolcaption) VALUES
('Flattening Die','0', 'Flattening dies are used for hemming or flattening acute angle bends.');
INSERT INTO `tool_library` (toolname,toolangles,toolcaption) VALUES
('Four Way Die','85', 'They are useful for jobbing where changes in die opening are frequently desired');
INSERT INTO `tool_library` (toolname,toolangles,toolcaption) VALUES
('U bending dies','U shapes', 'Channeling bending');
select concat('TL', toolid) as toolid, toolname from tool_library;
-- INSERT INTO `tool_library` (toolname,toolangles,toolcaption) VALUES
-- ('','', '');
-- INSERT INTO `tool_library` (toolname,toolangles,toolcaption) VALUES
-- ('','', '');
-- INSERT INTO `tool_library` (toolname,toolangles,toolcaption) VALUES
-- ('','', '');
-- INSERT INTO `tool_library` (toolname,toolangles,toolcaption) VALUES
-- ('','', '');
-- INSERT INTO `tool_library` (toolname,toolangles,toolcaption) VALUES
-- ('','', '');
-- INSERT INTO `tool_library` (toolname,toolangles,toolcaption) VALUES
-- ('','', '');
-- INSERT INTO `tool_library` (toolname,toolangles,toolcaption) VALUES
-- ('','', '');
-- INSERT INTO `tool_library` (toolname,toolangles,toolcaption) VALUES
-- ('','', '');

CREATE TABLE `machine_library` (
`machineid` INTEGER(4) unsigned zerofill NOT NULL AUTO_INCREMENT,
`machinename` VARCHAR(50) NOT NULL unique,
`machinedetails` VARCHAR(200) NOT NULL,
`machineidle` boolean not null default 0,
PRIMARY KEY (`machineid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE `projects` (
`projectid` INTEGER(3) unsigned zerofill NOT NULL AUTO_INCREMENT,
`projectname` VARCHAR(50) NOT NULL unique,
`projectdescription` VARCHAR(200) NOT NULL,
`projectmaterialid` INTEGER NOT NULL,
`projectownerid` INTEGER NOT NULL,
`projectfileid` INTEGER NOT NULL DEFAULT 0,
`projectfeatureid` INTEGER NOT NULL DEFAULT 0,
`projectcreationdate` TIMESTAMP NOT NULL,
`projectmodifieddate` TIMESTAMP NOT NULL,
`projectcomplete` boolean not null default 0,
`projectprocessplan` VARCHAR(200) DEFAULT NULL,
PRIMARY KEY (`projectid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- ALTER TABLE `projects` ADD projectcomplete boolean not null default 0;
-- ALTER TABLE `projects` ADD projectprocessplan VARCHAR(200) DEFAULT NULL;

CREATE TABLE `files` (
`fileid` INTEGER NOT NULL AUTO_INCREMENT,
`fileuserid` INTEGER NOT NULL,
`fileprojectid` INTEGER NOT NULL,
`filename` VARCHAR(50) NOT NULL unique,
`filetype` VARCHAR(20) NOT NULL,
`filesize` INTEGER NOT NULL,
`filecaption` VARCHAR(255) NOT NULL DEFAULT "",
`filemodelmaterialid` INTEGER NOT NULL,
`filemodelunits` VARCHAR(10) NULL,
`fileuploaddate` TIMESTAMP NOT NULL,
PRIMARY KEY (`fileid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE `features` (
`featureid` INTEGER NOT NULL AUTO_INCREMENT,
`projectid` INTEGER NOT NULL,
`fileid` INTEGER NOT NULL,
`bend_id` INTEGER NOT NULL unique,
`face1_id` INTEGER NOT NULL,
`face2_id` INTEGER NOT NULL,
`angle` INTEGER NOT NULL,
`bend_loop_id` INTEGER NOT NULL,
`bend_unit` varchar(10) NOT NULL,
`bend_length` FLOAT NOT NULL,
`bend_thickness` FLOAT NOT NULL,
`bend_radius` FLOAT NOT NULL,
`bend_height` FLOAT NOT NULL,
`bending_force` FLOAT NOT NULL,
`toolid` INTEGER NOT NULL DEFAULT 00000,
PRIMARY KEY (`featureid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- CREATE TABLE `bends` (
-- `bendid` INTEGER NOT NULL unique,
-- `fileid` INTEGER NOT NULL,
-- `bend_id` INTEGER NOT NULL,
-- `face1_id` INTEGER NOT NULL,
-- `face2_id` INTEGER NOT NULL,
-- `angle` INTEGER NOT NULL,
-- `bend_loop_id` INTEGER NOT NULL,
-- `bend_length` FLOAT NOT NULL,
-- `bend_thickness` FLOAT NOT NULL,
-- `bend_radius` FLOAT NOT NULL,
-- `bend_height` FLOAT NOT NULL,
-- `bending_force` FLOAT NOT NULL,
-- PRIMARY KEY (`b_id`)
-- ) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- ODO: add usage number
-- ODO: add tool material
-- ODO: add projects Table completion
-- ODO: add feature table
-- ODO: upload folder per user

CREATE TABLE `tstrength` (
`m_id` INTEGER NOT NULL AUTO_INCREMENT,
`material` varchar(255) NOT NULL UNIQUE,
`tensile_strength` FLOAT NOT NULL,
PRIMARY KEY (`m_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

INSERT INTO `tstrength` (material, tensile_strength) VALUES ('Steel, 0.2% Carbon, hot rolled', 415);
INSERT INTO `tstrength` (material, tensile_strength) VALUES ('Steel, 0.2% Carbon, cold rolled', 550);
INSERT INTO `tstrength` (material, tensile_strength) VALUES ('Steel, 0.8% Carbon, hot rolled', 825);
INSERT INTO `tstrength` (material, tensile_strength) VALUES ('Stainless Steel', 622);
INSERT INTO `tstrength` (material, tensile_strength) VALUES ('Aluminum', 495);
INSERT INTO `tstrength` (material, tensile_strength) VALUES ('Titanium Alloy', 1069);
INSERT INTO `tstrength` (material, tensile_strength) VALUES ('Brass Annealed', 331);

-- INSERT INTO `projects` (projectname, projectdescription, projectmaterialid, projectownerid, projectfileid )
-- VALUES ('First', 'At w3schools.com you will learn how to make a website. We offer free tutorials in all web development technologies.',
-- 1, 1, 1);

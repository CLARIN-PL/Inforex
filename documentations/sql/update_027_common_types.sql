CREATE TABLE IF NOT EXISTS `annotation_types_common` (
  `annotation_name` varchar(32) NOT NULL,
  KEY `annotation_name` (`annotation_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `annotation_types_common`
--

INSERT INTO `annotation_types_common` (`annotation_name`) VALUES
('admin1_nam'),
('admin2_nam'),
('admin3_nam'),
('award_nam'),
('brand_nam'),
('city_nam'),
('country_nam'),
('event_nam'),
('facility_nam'),
('historical_region_nam'),
('institution_nam'),
('nation_nam'),
('organization_nam'),
('person_add_nam'),
('person_first_nam'),
('person_last_nam'),
('person_nam'),
('road_nam'),
('title_nam');

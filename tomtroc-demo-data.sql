-- phpMyAdmin SQL Dump
-- version 5.2.2deb1+deb13u1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jul 06, 2026 at 05:03 PM
-- Server version: 11.8.6-MariaDB-0+deb13u1 from Debian
-- PHP Version: 8.4.23

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `tomtroc`
--
CREATE DATABASE IF NOT EXISTS `tomtroc` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_uca1400_ai_ci;
USE `tomtroc`;

-- --------------------------------------------------------

--
-- Table structure for table `books`
--

DROP TABLE IF EXISTS `books`;
CREATE TABLE `books` (
  `book_id` int(10) UNSIGNED NOT NULL,
  `title` varchar(150) NOT NULL,
  `author` varchar(150) NOT NULL,
  `image_path` varchar(255) NOT NULL DEFAULT '/upload/books/default-book.png',
  `description` text NOT NULL,
  `availability` enum('NOT-AVAILABLE','AVAILABLE') NOT NULL DEFAULT 'AVAILABLE',
  `fk_member_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Dumping data for table `books`
--

INSERT INTO `books` (`book_id`, `title`, `author`, `image_path`, `description`, `availability`, `fk_member_id`) VALUES
(1, 'Esther', 'Alabaster', '/upload/books/Esther.jpg', 'Le Livre d\'Esther est l\'un des récits les plus captivants de l\'Ancien Testament. Il raconte l\'histoire d\'Esther, une jeune femme juive devenue reine de Perse, qui se retrouve confrontée à un choix décisif : risquer sa propre vie pour sauver son peuple d\'un complot d\'extermination. Élevée par son cousin Mardochée, Esther dissimule d\'abord ses origines juives avant de révéler son identité au roi Assuérus afin de déjouer les machinations du puissant Haman, conseiller du souverain. Grâce à son courage, sa sagesse et sa détermination, elle parvient à inverser le cours des événements et à empêcher un massacre annoncé.\r\n\r\nCe livre se distingue par son intrigue riche en rebondissements, où les retournements de situation, les coïncidences apparentes et les décisions humaines jouent un rôle essentiel. Bien que le nom de Dieu n\'y soit jamais explicitement mentionné, la tradition y voit une illustration de la providence divine agissant discrètement à travers les événements.\r\n\r\nL\'œuvre met en lumière des thèmes universels tels que le courage face à l\'injustice, la fidélité à ses convictions, la responsabilité individuelle et la défense des plus vulnérables. À l\'origine de la fête juive de Pourim, le Livre d\'Esther demeure un texte majeur de la tradition biblique. Son message d\'espérance, de résilience et de confiance continue d\'inspirer les lecteurs, faisant d\'Esther une figure emblématique de bravoure, de sagesse et d\'engagement face à l\'adversité. ', 'AVAILABLE', 1),
(2, 'The Kinfolk Table', 'Nathan Williams', '/upload/books/The_Kinfolk_Table.jpg', 'J\'ai récemment plongé dans les pages de \'The Kinfolk Table\' et j\'ai été enchanté par cette œuvre captivante. Ce livre va bien au-delà d\'une simple collection de recettes ; il célèbre l\'art de partager des moments authentiques autour de la table.\r\n\r\nLes photographies magnifiques et le ton chaleureux captivent dès le départ, transportant le lecteur dans un voyage à travers des recettes et des histoires qui mettent en avant la beauté de la simplicité et de la convivialité.\r\n\r\nChaque page est une invitation à ralentir, à savourer et à créer des souvenirs durables avec les êtres chers.\r\n\r\n\'The Kinfolk Table\' incarne parfaitement l\'esprit de la cuisine et de la camaraderie, et il est certain que ce livre trouvera une place spéciale dans le cœur de tout amoureux de la cuisine et des rencontres inspirantes.', 'AVAILABLE', 2),
(3, 'Wabi Sabi', 'Beth Kempton', '/upload/books/Wabi_Sabi.jpg', 'Wabi Sabi de Beth Kempton est une invitation à ralentir et à redécouvrir la beauté des choses simples. Inspiré d\'une philosophie japonaise ancestrale, l\'ouvrage explore le concept du wabi-sabi, une manière de voir le monde qui valorise l\'imperfection, l\'impermanence et l\'authenticité. À travers son expérience personnelle, ses voyages au Japon et de nombreux exemples tirés de la culture japonaise, Beth Kempton montre comment cette philosophie peut transformer notre rapport au temps, aux objets, à la nature et à nous-mêmes.\r\n\r\nL\'auteure propose un cheminement fait de réflexions, de récits et d\'exercices pratiques pour apprendre à apprécier le moment présent, à accepter ce qui ne peut être contrôlé et à trouver de la sérénité dans un quotidien souvent marqué par la recherche de performance et de perfection. Elle aborde des thèmes tels que le minimalisme, la gratitude, la simplicité volontaire, les cycles de la vie et l\'importance de cultiver des relations sincères avec les autres et avec son environnement.\r\n\r\nPlus qu\'un simple livre de développement personnel, Wabi Sabi est une philosophie de vie qui invite à porter un regard différent sur le monde. Il encourage à accueillir les imperfections comme une richesse, à ralentir pour mieux savourer chaque instant et à créer un mode de vie plus harmonieux et plus conscient. Accessible et inspirant, cet ouvrage offre des pistes concrètes pour retrouver un équilibre durable et redonner du sens aux petits gestes du quotidien.', 'AVAILABLE', 2),
(4, 'Milk & honey', 'Rupi Kaur', '/upload/books/Milk_&_honey.jpg', 'Milk and Honey de Rupi Kaur est un recueil de poésie contemporaine qui explore avec sensibilité les expériences universelles de l\'amour, de la perte, de la souffrance et de la reconstruction. Structuré en quatre parties – the hurting, the loving, the breaking et the healing –, l\'ouvrage accompagne le lecteur à travers un cheminement émotionnel où les blessures du passé laissent progressivement place à l\'acceptation et à la guérison. Chaque poème, rédigé dans un style épuré et direct, est accompagné d\'illustrations minimalistes réalisées par l\'auteure, renforçant l\'intensité des émotions exprimées.\r\n\r\nÀ travers des textes courts mais percutants, Rupi Kaur aborde des sujets tels que les violences, les relations toxiques, le deuil, la féminité, l\'identité, l\'estime de soi et la résilience. Son écriture, volontairement simple et accessible, permet de transmettre des émotions profondes sans artifices. Elle invite le lecteur à reconnaître ses propres fragilités tout en célébrant la force nécessaire pour se relever après les épreuves.\r\n\r\nPublié initialement de manière indépendante, Milk and Honey est devenu un phénomène international et a contribué à populariser une nouvelle génération de poésie contemporaine. Son message met en avant l\'importance de l\'amour de soi, de la reconstruction personnelle et de l\'acceptation des cicatrices comme partie intégrante de notre histoire. À la fois intime et universel, ce recueil touche des lecteurs de tous horizons et rappelle que, comme le lait et le miel, la douceur peut naître même après les moments les plus difficiles.', 'AVAILABLE', 4),
(5, 'Delight!', 'Justin Rossow', '/upload/books/Delight.jpg', 'Delight! de Justin Rossow est un ouvrage qui invite les lecteurs à redécouvrir la joie profonde que procure une relation authentique avec Dieu. S\'appuyant sur les enseignements de la Bible et sur des exemples concrets de la vie quotidienne, l\'auteur montre que la foi ne se résume pas à un ensemble de règles ou d\'obligations, mais qu\'elle peut devenir une source constante d\'émerveillement, d\'espérance et de satisfaction. Il encourage chacun à cultiver une vie spirituelle centrée sur la gratitude, la confiance et la découverte des bénédictions présentes dans les gestes les plus simples.\r\n\r\nTout au long du livre, Justin Rossow explore les obstacles qui empêchent souvent de ressentir cette joie durable : les inquiétudes, les attentes irréalistes, les difficultés de la vie ou encore la comparaison avec les autres. Il propose des pistes de réflexion et des conseils pratiques pour développer une foi plus vivante, renforcer sa relation avec Dieu et apprendre à trouver la paix même au milieu des épreuves. L\'ouvrage met également en avant l\'importance de la prière, de la méditation des Écritures et du service envers les autres comme moyens de nourrir une joie authentique.\r\n\r\nAccessible et encourageant, Delight! s\'adresse à tous ceux qui souhaitent approfondir leur cheminement spirituel et vivre une foi plus épanouissante au quotidien. Son message rappelle que la véritable joie ne dépend pas uniquement des circonstances, mais trouve sa source dans une confiance renouvelée en Dieu et dans la capacité à reconnaître sa présence dans chaque étape de la vie.', 'AVAILABLE', 5),
(6, 'Milwaukee Mission', 'Elder Cooper Low', '/upload/books/Milwaukee_Mission.jpg', 'The Cooper Family – Milwaukee, Wisconsin Mission est un ouvrage commémoratif consacré à une mission réalisée dans la région de Milwaukee, dans le Wisconsin. À travers des photographies, des témoignages et des récits personnels, il retrace les rencontres, les expériences et les moments marquants vécus au cours de cette période de service.\r\n\r\nLe livre met en lumière les valeurs de foi, d\'engagement, de solidarité et de partage qui accompagnent la vie missionnaire, tout en offrant un aperçu du contexte culturel et humain de la région. Plus qu\'un simple album de souvenirs, il constitue une mémoire familiale et spirituelle, témoignant des liens créés avec les personnes rencontrées et des enseignements tirés de cette expérience.\r\n\r\nLes images et les textes permettent de revivre les événements importants, les défis relevés ainsi que les joies du quotidien, tout en soulignant l\'impact durable qu\'une mission peut avoir sur ceux qui la vivent.\r\n\r\nDestiné aux proches, aux membres de la communauté ou aux personnes intéressées par les récits de mission, cet ouvrage illustre l\'importance du service, de l\'entraide et de la transmission des souvenirs.\r\n\r\nIl constitue un témoignage personnel qui préserve une étape significative de la vie de ses auteurs et offre un regard authentique sur une expérience humaine et spirituelle vécue à Milwaukee.', 'AVAILABLE', 6),
(7, 'Minimalist Graphics', 'Julia Schonlau', '/upload/books/Minimalist_Graphics.jpg', 'Minimalist Graphics de Julia Schonlau est un ouvrage de référence consacré au design graphique minimaliste et à son efficacité dans la communication visuelle contemporaine. À travers une riche sélection de projets réalisés par des designers et des studios du monde entier, le livre démontre comment la simplicité, la clarté et l\'équilibre peuvent produire des créations percutantes. Logos, identités visuelles, affiches, livres, supports imprimés et autres réalisations illustrent le principe selon lequel « moins, c\'est mieux », en privilégiant l\'essentiel plutôt que la surcharge graphique.\r\n\r\nL\'ouvrage s\'ouvre sur une introduction retraçant l\'histoire du minimalisme dans le design graphique et son influence sur les tendances actuelles. Les projets sont ensuite organisés par catégories, permettant au lecteur d\'explorer différentes approches de la composition, de la typographie, de l\'utilisation des couleurs et des espaces négatifs. Chaque réalisation est accompagnée d\'informations sur son contexte de création ainsi que sur le studio ou le designer à son origine, offrant un éclairage précieux sur les choix créatifs et les méthodes employées.\r\n\r\nDestiné aussi bien aux graphistes professionnels qu\'aux étudiants et aux passionnés de design, Minimalist Graphics constitue une source d\'inspiration riche et intemporelle. Il met en évidence qu\'un design réussi ne repose pas sur la complexité, mais sur la capacité à transmettre un message clair avec un minimum d\'éléments. Véritable vitrine du graphisme contemporain, cet ouvrage invite à repenser la création visuelle en privilégiant la lisibilité, la cohérence et l\'élégance, tout en montrant que la simplicité est souvent le meilleur moyen de marquer durablement les esprits.', 'AVAILABLE', 7),
(8, 'Hygge', 'Meik Wiking', '/upload/books/Hygge.jpg', 'Hygge de Meik Wiking explore l\'un des concepts les plus emblématiques de la culture danoise : le hygge, un art de vivre fondé sur le bien-être, la convivialité et le plaisir des choses simples. Directeur du Happiness Research Institute de Copenhague, l\'auteur s\'appuie sur des années de recherches consacrées au bonheur pour expliquer pourquoi le Danemark figure régulièrement parmi les pays les plus heureux du monde. Il montre que le hygge ne se résume pas à une décoration chaleureuse ou à une soirée au coin du feu, mais qu\'il constitue une véritable philosophie de vie, centrée sur la présence, le partage et l\'appréciation des petits instants du quotidien.\r\n\r\nÀ travers des anecdotes, des études, des photographies et des conseils pratiques, Meik Wiking invite le lecteur à créer une atmosphère propice au calme et à la sérénité. Il aborde des thèmes tels que l\'éclairage, l\'aménagement de la maison, les repas entre amis, les traditions familiales, les plaisirs de l\'hiver ou encore l\'importance de ralentir dans un monde où tout s\'accélère. L\'ouvrage propose également des recettes, des idées d\'activités et des pistes pour intégrer le hygge dans la vie de tous les jours, quelles que soient les saisons.\r\n\r\nAccessible, inspirant et richement illustré, Hygge est bien plus qu\'un guide sur le mode de vie danois. Il invite chacun à repenser sa manière de vivre en privilégiant les relations humaines, la simplicité, la gratitude et le confort. Ce livre rappelle que le bonheur se construit souvent à travers les moments les plus ordinaires et que prendre le temps de les savourer est l\'une des clés d\'une vie plus équilibrée et plus épanouissante.', 'AVAILABLE', 4),
(9, 'Innovation', 'Matt Ridley', '/upload/books/Innovation.jpg', 'How Innovation Works de Matt Ridley explore les mécanismes qui permettent aux grandes innovations de transformer les sociétés. À rebours de l\'idée selon laquelle les inventions seraient le fruit du génie isolé de quelques individus, l\'auteur montre que l\'innovation est avant tout un processus collectif, progressif et cumulatif. À travers de nombreux exemples tirés de l\'histoire, des sciences, de la médecine, de l\'industrie et des technologies, il met en évidence le rôle essentiel de l\'expérimentation, de la collaboration et de l\'échange d\'idées dans l\'émergence des avancées majeures qui ont façonné notre monde.\r\n\r\nMatt Ridley retrace le parcours d\'innovations emblématiques, de la machine à vapeur aux vaccins, en passant par l\'électricité, l\'aviation et Internet. Il explique que les progrès les plus importants résultent souvent d\'améliorations successives plutôt que d\'inventions soudaines. L\'auteur insiste également sur l\'importance de la liberté d\'entreprendre, de la concurrence, des marchés ouverts et de la circulation des connaissances pour favoriser la créativité et accélérer le développement de nouvelles solutions. Il montre comment les erreurs, les échecs et les essais répétés constituent des étapes indispensables du processus d\'innovation.\r\n\r\nClair et richement documenté, How Innovation Works propose une réflexion accessible sur les conditions qui favorisent le progrès humain. L\'ouvrage invite le lecteur à porter un regard différent sur l\'histoire des découvertes et rappelle que les innovations naissent rarement du hasard. Elles sont le résultat d\'une accumulation de connaissances, d\'une curiosité permanente et de la capacité des individus à partager leurs idées pour résoudre les défis de leur époque.', 'AVAILABLE', 8),
(10, 'Psalms', 'Alabaster', '/upload/books/Psalms.jpg', 'Psalms de la collection Alabaster propose une redécouverte visuelle et contemplative du livre des Psaumes. Conçu comme un ouvrage mêlant texte biblique, photographie contemporaine et design minimaliste, il invite le lecteur à ralentir et à méditer sur les prières, les chants et les poèmes qui composent l\'un des livres les plus inspirants de la Bible. Fidèle au texte des Écritures, cette édition met en valeur la richesse littéraire des Psaumes grâce à une mise en page soignée et à des images évocatrices qui prolongent le sens des passages sans en détourner le message.\r\n\r\nLes Psaumes abordent toute la diversité de l\'expérience humaine : la joie, la gratitude, la peur, le doute, le repentir, la confiance et l\'espérance. À travers les paroles de David et d\'autres auteurs bibliques, ils expriment une relation authentique avec Dieu, où les émotions les plus profondes trouvent leur place dans la prière. Cette édition Alabaster accompagne cette lecture par une direction artistique épurée qui favorise le recueillement et la réflexion personnelle, faisant dialoguer les textes sacrés avec des photographies inspirées de la nature, de la lumière et des paysages.\r\n\r\nPlus qu\'une simple édition de la Bible, Psalms est une expérience de lecture immersive qui s\'adresse aussi bien aux croyants qu\'aux amateurs de beaux livres. En associant esthétique contemporaine et texte biblique, Alabaster propose une nouvelle manière d\'aborder les Écritures, où la beauté visuelle devient un support à la méditation. Cet ouvrage constitue une invitation à retrouver le calme, à nourrir sa vie spirituelle et à redécouvrir l\'intemporalité des Psaumes dans un format élégant et inspirant.', 'AVAILABLE', 9),
(11, 'Thinking, Fast & Slow', 'Daniel Kahneman', '/upload/books/Thinking,_Fast_&_Slow.jpg', 'Thinking, Fast and Slow de Daniel Kahneman est un ouvrage majeur consacré au fonctionnement de la pensée humaine et aux mécanismes qui influencent nos décisions. Lauréat du prix Nobel d\'économie, Daniel Kahneman s\'appuie sur plusieurs décennies de recherches en psychologie cognitive et en économie comportementale pour expliquer que notre cerveau fonctionne selon deux modes de pensée distincts. Le Système 1, rapide, intuitif et automatique, permet de réagir instantanément aux situations du quotidien. Le Système 2, plus lent, réfléchi et analytique, intervient lorsque nous devons résoudre des problèmes complexes, effectuer des calculs ou remettre en question nos intuitions.\r\n\r\nÀ travers de nombreuses expériences et exemples concrets, l\'auteur met en évidence les biais cognitifs qui influencent nos jugements, souvent à notre insu. Il explore notamment l\'excès de confiance, l\'effet d\'ancrage, l\'aversion aux pertes, les heuristiques de décision et les erreurs de raisonnement qui affectent aussi bien les choix individuels que les décisions économiques, politiques ou professionnelles. Kahneman montre que, malgré notre impression d\'être rationnels, nos décisions sont fréquemment guidées par des raccourcis mentaux qui peuvent conduire à des erreurs prévisibles.\r\n\r\nAccessible malgré la richesse de son contenu scientifique, Thinking, Fast and Slow offre des clés pour mieux comprendre notre manière de penser et améliorer notre prise de décision. L\'ouvrage invite le lecteur à reconnaître les limites de son intuition, à développer un esprit critique et à adopter une approche plus réfléchie face aux choix importants. Devenu une référence mondiale en psychologie et en économie comportementale, ce livre constitue une lecture essentielle pour toute personne souhaitant mieux comprendre le fonctionnement de l\'esprit humain.', 'AVAILABLE', 10),
(12, 'A Book Full Of Hope', 'Emily Coxhead', '/upload/books/A_Book_Full_Of_Hope.jpg', 'A Book Full of Hope d\'Emily Coxhead est un ouvrage illustré qui invite à retrouver l\'espoir, la joie et l\'optimisme à travers les petits moments du quotidien. Créatrice du projet The Happy News, l\'auteure rassemble dans ce livre une collection de pensées positives, d\'histoires inspirantes, de citations, d\'illustrations colorées et de messages bienveillants destinés à rappeler que, même dans les périodes les plus difficiles, il existe toujours des raisons de garder confiance en l\'avenir. Son approche, à la fois simple et sincère, encourage le lecteur à porter un regard plus doux sur lui-même et sur le monde qui l\'entoure.\r\n\r\nAu fil des pages, Emily Coxhead aborde des thèmes tels que la résilience, la gratitude, la santé mentale, l\'amitié, la compassion et l\'importance des gestes de gentillesse. Chaque illustration est conçue pour transmettre une émotion positive et offrir un moment de réconfort, tandis que les textes invitent à ralentir, à apprécier le présent et à reconnaître les nombreuses sources d\'espoir qui nous entourent. Sans nier les difficultés de la vie, l\'auteure montre qu\'il est possible de traverser les épreuves en s\'appuyant sur la solidarité, l\'écoute et les petites victoires du quotidien.\r\n\r\nVéritable parenthèse de douceur, A Book Full of Hope est un livre que l\'on peut ouvrir à n\'importe quelle page pour y trouver une pensée encourageante ou une source d\'inspiration. Il constitue un cadeau idéal pour une personne traversant une période difficile, mais aussi une lecture réconfortante pour tous ceux qui souhaitent cultiver un état d\'esprit plus positif. Accessible à tous les âges, cet ouvrage rappelle que l\'espoir est une force qui se nourrit des petites choses et des liens que nous tissons avec les autres.', 'AVAILABLE', 11),
(13, 'The Subtle Art of Not Giving a F*ck', 'Mark Manson', '/upload/books/The_Subtle_Art_Of.jpg', 'The Subtle Art of Not Giving a F*ck de Mark Manson propose une approche originale du développement personnel en remettant en question les discours traditionnels qui prônent une recherche permanente du bonheur et de la pensée positive. Avec un ton direct, souvent provocateur et empreint d\'humour, l\'auteur défend l\'idée que le véritable épanouissement ne consiste pas à éviter les difficultés, mais à accepter que la souffrance, l\'échec et l\'incertitude font naturellement partie de l\'existence. Selon lui, notre temps et notre énergie étant limités, il est essentiel de choisir avec discernement les causes, les objectifs et les préoccupations qui méritent réellement notre attention.\r\n\r\nÀ travers des anecdotes personnelles, des références à la psychologie, à la philosophie et à l\'histoire, Mark Manson montre que nos valeurs influencent profondément nos décisions et notre bien-être. Il invite le lecteur à abandonner la quête de la perfection et à accepter ses limites, ses erreurs et sa vulnérabilité comme des éléments indispensables à une vie plus authentique. L\'ouvrage aborde des thèmes tels que la responsabilité individuelle, le rapport à l\'échec, les relations humaines, la peur du jugement et la recherche de sens.\r\n\r\nPlus qu\'un simple guide de motivation, The Subtle Art of Not Giving a F*ck encourage à vivre de manière plus consciente en se concentrant sur ce qui compte réellement. Son message principal est que le bonheur ne naît pas de l\'absence de problèmes, mais de notre capacité à affronter les défis avec lucidité, courage et des valeurs solides. Grâce à son style accessible et sans détour, ce livre est devenu une référence contemporaine du développement personnel.', 'AVAILABLE', 12),
(14, 'Narnia', 'C.S Lewis', '/upload/books/Narnia.jpg', 'Les Chroniques de Narnia de C. S. Lewis constituent l\'une des œuvres les plus emblématiques de la littérature fantastique. Cette série de sept romans transporte le lecteur dans le monde merveilleux de Narnia, un royaume où les animaux parlent, où la magie est omniprésente et où les forces du bien affrontent celles du mal. À travers les aventures de plusieurs enfants venus de notre monde, C. S. Lewis imagine un univers riche en créatures fantastiques, en paysages enchanteurs et en personnages inoubliables, parmi lesquels le lion Aslan, figure de sagesse, de courage et de sacrifice. Chaque livre raconte une histoire indépendante tout en participant à une vaste fresque retraçant la création, l\'histoire et le destin de Narnia.\r\n\r\nAu fil des récits, les héros sont confrontés à des épreuves qui les poussent à grandir, à faire preuve de courage et à défendre la justice face à la tyrannie. Les thèmes de l\'amitié, de la loyauté, du pardon, du sacrifice et de l\'espérance traversent l\'ensemble de la saga, offrant plusieurs niveaux de lecture. Les plus jeunes y trouveront des aventures captivantes, tandis que les lecteurs adultes pourront y découvrir des réflexions philosophiques et spirituelles inspirées de la foi chrétienne de l\'auteur.\r\n\r\nMêlant imagination, émotion et poésie, Les Chroniques de Narnia ont marqué plusieurs générations de lecteurs et demeurent une référence incontournable de la fantasy. Grâce à son écriture accessible, à son univers foisonnant et à ses personnages attachants, cette œuvre continue d\'inviter petits et grands à franchir les portes de l\'imaginaire et à redécouvrir la puissance intemporelle des contes.', 'AVAILABLE', 13),
(15, 'Company Of One', 'Paul Jarvis', '/upload/books/Company_Of_One.jpg', 'Company of One de Paul Jarvis propose une approche originale de l\'entrepreneuriat en remettant en question l\'idée selon laquelle une entreprise doit forcément grandir pour réussir. L\'auteur défend le concept de « Company of One » un modèle où la réussite repose sur la rentabilité, la liberté et la simplicité plutôt que sur une croissance permanente. Selon lui, rester volontairement à taille humaine permet de mieux maîtriser son activité, de préserver son autonomie et de construire une entreprise au service de la vie que l\'on souhaite mener.\r\n\r\nÀ travers de nombreux exemples, Paul Jarvis montre qu\'il est possible de développer une activité prospère sans multiplier les employés, les investissements ou les risques. Il explique que la croissance doit être un choix réfléchi et non une obligation imposée par les standards du monde des affaires. L\'ouvrage aborde également la fidélisation des clients, la création de valeur, la productivité, l\'automatisation et l\'importance de définir ses propres critères de réussite.\r\n\r\nAccessible et concret, Company of One s\'adresse aux entrepreneurs, freelances et dirigeants de petites entreprises qui souhaitent bâtir une activité durable, rentable et alignée avec leurs valeurs. Plus qu\'un guide pratique, ce livre invite à repenser la notion même de succès en démontrant qu\'une entreprise peut prospérer sans rechercher une expansion constante. L\'auteur encourage également à privilégier des relations de confiance avec les clients, à limiter la complexité organisationnelle et à concentrer ses efforts sur la qualité des produits ou des services. En proposant une vision plus équilibrée de l\'entrepreneuriat, il rappelle que la réussite ne dépend pas uniquement de la taille d\'une entreprise, mais de sa capacité à répondre durablement aux besoins de ses clients tout en offrant à son fondateur une liberté réelle, une stabilité financière et un mode de vie choisi. Cette approche reste pertinente pour les entrepreneurs d\'aujourd\'hui. Merci beaucoup.', 'AVAILABLE', 14),
(16, 'The Two Towers', 'J.R.R Tolkien', '/upload/books/The_Two_Towers.jpg', 'The Two Towers est le deuxième tome de la trilogie Le Seigneur des Anneaux de J. R. R. Tolkien. L\'histoire reprend après la séparation de la Communauté de l\'Anneau, obligeant ses membres à poursuivre leur quête chacun de leur côté. Tandis que Frodon Sacquet et Sam Gamegie poursuivent leur dangereux voyage vers le Mordor afin de détruire l\'Anneau Unique, ils s\'en remettent à Gollum, une créature aussi misérable qu\'imprévisible, dont le destin est intimement lié à celui de l\'Anneau. Leur progression devient de plus en plus périlleuse à mesure que les forces de Sauron étendent leur influence sur la Terre du Milieu.\r\n\r\nParallèlement, Aragorn, Legolas et Gimli partent à la recherche de Merry et Pippin, capturés par les Orques. Leur route les conduit au royaume du Rohan, où le roi Théoden doit faire face aux armées de Saroumane, autrefois allié des peuples libres. Les héros prennent alors part à la légendaire bataille du Gouffre de Helm, un affrontement décisif qui met en lumière le courage, la solidarité et la détermination des défenseurs face à un ennemi largement supérieur en nombre.\r\n\r\nÀ travers ce récit, Tolkien approfondit les thèmes de l\'amitié, du sacrifice, de la loyauté et de la lutte contre la corruption du pouvoir. Il enrichit également son univers en développant l\'histoire, les peuples et les paysages de la Terre du Milieu. Alternant scènes d\'action, moments de tension et instants plus contemplatifs, The Two Towers constitue une étape essentielle de la saga. Ce roman prépare les événements du dernier tome tout en offrant une aventure épique, portée par une écriture immersive et des personnages devenus emblématiques de la littérature fantastique.', 'AVAILABLE', 15);

-- --------------------------------------------------------

--
-- Table structure for table `members`
--

DROP TABLE IF EXISTS `members`;
CREATE TABLE `members` (
  `member_id` int(10) UNSIGNED NOT NULL,
  `username` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_uca1400_as_cs NOT NULL,
  `email` varchar(150) NOT NULL,
  `avatar_path` varchar(255) NOT NULL DEFAULT '/upload/avatars/default-avatar.png',
  `password_hash` varchar(255) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `status` enum('NOT-VALIDATED','VALIDATED') NOT NULL DEFAULT 'VALIDATED',
  `notification_count` int(10) UNSIGNED NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Dumping data for table `members`
--

INSERT INTO `members` (`member_id`, `username`, `email`, `avatar_path`, `password_hash`, `created_at`, `updated_at`, `status`, `notification_count`) VALUES
(1, 'CamilleClubLit', 'CamilleClubLit@mail.com', '/upload/avatars/default-avatar.png', '$2y$12$PeOF4MjHOl0i96paAXi/FeN4al6nC2kWte58DA30VXPJRof5MQ.YK', '2026-06-24 01:24:21', '2026-06-24 01:42:56', 'VALIDATED', 0),
(2, 'Alexlecture', 'Alexlecture@mail.com', '/upload/avatars/Alexlecture.jpg', '$2y$12$PeOF4MjHOl0i96paAXi/FeN4al6nC2kWte58DA30VXPJRof5MQ.YK', '2025-06-24 01:24:21', '2026-06-24 01:42:56', 'VALIDATED', 0),
(3, 'nathalire', 'nathalie@mail.com', '/upload/avatars/nathalire.jpg', '$2y$12$PeOF4MjHOl0i96paAXi/FeN4al6nC2kWte58DA30VXPJRof5MQ.YK', '2026-06-24 01:24:21', '2026-06-24 01:42:56', 'VALIDATED', 0),
(4, 'Hugo1990_12', 'Hugo1990_12@mail.com', '/upload/avatars/default-avatar.png', '$2y$12$PeOF4MjHOl0i96paAXi/FeN4al6nC2kWte58DA30VXPJRof5MQ.YK', '2026-06-24 01:24:21', '2026-06-24 01:42:56', 'VALIDATED', 0),
(5, 'Juju1432', 'Juju1432@mail.com', '/upload/avatars/default-avatar.png', '$2y$12$PeOF4MjHOl0i96paAXi/FeN4al6nC2kWte58DA30VXPJRof5MQ.YK', '2026-06-24 01:24:21', '2026-06-24 01:42:56', 'VALIDATED', 0),
(6, 'Christiane75014', 'Christiane75014@mail.com', '/upload/avatars/default-avatar.png', '$2y$12$PeOF4MjHOl0i96paAXi/FeN4al6nC2kWte58DA30VXPJRof5MQ.YK', '2026-06-24 01:24:21', '2026-06-24 01:42:56', 'VALIDATED', 0),
(7, 'Hamzalecture', 'Hamzalecture@mail.com', '/upload/avatars/default-avatar.png', '$2y$12$PeOF4MjHOl0i96paAXi/FeN4al6nC2kWte58DA30VXPJRof5MQ.YK', '2026-06-24 01:24:21', '2026-06-24 01:42:56', 'VALIDATED', 0),
(8, 'Lou&Ben50', 'Lou&Ben50@mail.com', '/upload/avatars/default-avatar.png', '$2y$12$PeOF4MjHOl0i96paAXi/FeN4al6nC2kWte58DA30VXPJRof5MQ.YK', '2026-06-24 01:24:21', '2026-06-24 01:42:56', 'VALIDATED', 0),
(9, 'Lolobzh', 'Lolobzh@mail.com', '/upload/avatars/default-avatar.png', '$2y$12$PeOF4MjHOl0i96paAXi/FeN4al6nC2kWte58DA30VXPJRof5MQ.YK', '2026-06-24 01:24:21', '2026-06-24 01:42:56', 'VALIDATED', 0),
(10, 'Sas634', 'Sas634@mail.com', '/upload/avatars/default-avatar.png', '$2y$12$PeOF4MjHOl0i96paAXi/FeN4al6nC2kWte58DA30VXPJRof5MQ.YK', '2026-06-24 01:24:21', '2026-06-24 01:42:56', 'VALIDATED', 0),
(11, 'ML95', 'ML95@mail.com', '/upload/avatars/default-avatar.png', '$2y$12$PeOF4MjHOl0i96paAXi/FeN4al6nC2kWte58DA30VXPJRof5MQ.YK', '2026-06-24 01:24:21', '2026-06-24 01:42:56', 'VALIDATED', 0),
(12, 'Verogo33', 'Verogo33@mail.com', '/upload/avatars/default-avatar.png', '$2y$12$PeOF4MjHOl0i96paAXi/FeN4al6nC2kWte58DA30VXPJRof5MQ.YK', '2026-06-24 01:24:21', '2026-06-24 01:42:56', 'VALIDATED', 0),
(13, 'AnnikaBrahms', 'AnnikaBrahms@mail.com', '/upload/avatars/default-avatar.png', '$2y$12$PeOF4MjHOl0i96paAXi/FeN4al6nC2kWte58DA30VXPJRof5MQ.YK', '2026-06-24 01:24:21', '2026-06-24 01:42:56', 'VALIDATED', 0),
(14, 'Victoirefabr912', 'Victoirefabr912@mail.com', '/upload/avatars/default-avatar.png', '$2y$12$PeOF4MjHOl0i96paAXi/FeN4al6nC2kWte58DA30VXPJRof5MQ.YK', '2026-06-24 01:24:21', '2026-06-24 01:42:56', 'VALIDATED', 0),
(15, 'Lotrfanclub67', 'Lotrfanclub67@mail.com', '/upload/avatars/default-avatar.png', '$2y$12$PeOF4MjHOl0i96paAXi/FeN4al6nC2kWte58DA30VXPJRof5MQ.YK', '2026-06-24 01:24:21', '2026-06-24 01:42:56', 'VALIDATED', 0);

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

DROP TABLE IF EXISTS `messages`;
CREATE TABLE `messages` (
  `message_id` int(10) UNSIGNED NOT NULL,
  `content` text NOT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `sent_at` datetime NOT NULL DEFAULT current_timestamp(),
  `modified_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `fk_from_member_id` int(10) UNSIGNED NOT NULL,
  `fk_to_member_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Dumping data for table `messages`
--

INSERT INTO `messages` (`message_id`, `content`, `is_read`, `sent_at`, `modified_at`, `fk_from_member_id`, `fk_to_member_id`) VALUES
(56, 'Hello How are you ?', 0, '2026-06-30 02:19:09', '2026-06-30 02:19:09', 2, 13),
(57, 'Hey Im fine and you ?', 1, '2026-06-30 02:19:09', '2026-07-06 18:03:36', 13, 2),
(58, 'I\'m fine too', 0, '2026-06-30 02:20:03', '2026-06-30 02:20:03', 2, 13),
(59, 'Hey bro wazzup ?', 1, '2026-06-30 02:20:03', '2026-07-06 18:03:34', 3, 2);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `books`
--
ALTER TABLE `books`
  ADD PRIMARY KEY (`book_id`),
  ADD KEY `fk_member_id` (`fk_member_id`);

--
-- Indexes for table `members`
--
ALTER TABLE `members`
  ADD PRIMARY KEY (`member_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`message_id`),
  ADD KEY `fk_from_member_id` (`fk_from_member_id`),
  ADD KEY `fk_to_member_id` (`fk_to_member_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `books`
--
ALTER TABLE `books`
  MODIFY `book_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `members`
--
ALTER TABLE `members`
  MODIFY `member_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `message_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=60;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `books`
--
ALTER TABLE `books`
  ADD CONSTRAINT `books_ibfk_1` FOREIGN KEY (`fk_member_id`) REFERENCES `members` (`member_id`) ON DELETE CASCADE;

--
-- Constraints for table `messages`
--
ALTER TABLE `messages`
  ADD CONSTRAINT `messages_ibfk_1` FOREIGN KEY (`fk_from_member_id`) REFERENCES `members` (`member_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `messages_ibfk_2` FOREIGN KEY (`fk_to_member_id`) REFERENCES `members` (`member_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

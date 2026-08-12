<?php

namespace Database\Seeders;

use App\Models\Subject;
use App\Models\SubjectCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * Seeds the Indian school (Class 1-12, CBSE & MP Board) and engineering
 * (B.E. & M.Tech, university-wise) curriculum as subject categories/subjects
 * so tutors can attach their courses to a recognised class/board/branch.
 *
 * Syllabus text is a representative topic outline (broadly aligned with the
 * NCERT/AICTE model curriculum both boards/universities follow), not a
 * verbatim reproduction of any official syllabus document.
 */
class CourseCatalogSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedSchoolCurriculum();
        $this->seedHigherEducation();
    }

    protected function seedSchoolCurriculum(): void
    {
        $boards = ['CBSE', 'MP Board'];

        $primaryLower = [
            'English' => 'Alphabet and phonics; simple words and sentences; reading short stories; basic grammar (nouns, pronouns); rhymes and recitation',
            'Hindi' => 'वर्णमाला (स्वर-व्यंजन); सरल शब्द व वाक्य निर्माण; कहानी व कविता वाचन; बुनियादी व्याकरण (संज्ञा, सर्वनाम); चित्र वर्णन',
            'Mathematics' => 'Numbers 1-100; addition and subtraction; shapes and patterns; measurement basics; money (coins and notes); time (clock reading)',
        ];

        $primaryUpper = [
            'English' => 'Grammar (tenses, articles, prepositions); comprehension passages; vocabulary building; letter and paragraph writing; poetry appreciation',
            'Hindi' => 'व्याकरण (काल, विलोम शब्द, पर्यायवाची); गद्य व पद्य बोध; निबंध व पत्र लेखन; शब्द भंडार विस्तार',
            'Mathematics' => 'Numbers up to 1,00,000; four operations; fractions and decimals; geometry (2D shapes, perimeter); data handling; money and time',
            'Environmental Studies (EVS)' => 'Family and community; plants and animals; food and health; water and natural resources; our environment; transport and communication',
        ];

        $middle = [
            'English' => 'Grammar (clauses, voice, direct-indirect speech); prose and poetry; letter, notice and story writing; comprehension; vocabulary building',
            'Hindi' => 'व्याकरण (समास, संधि, अलंकार); गद्य-पद्य संकलन; पत्र व निबंध लेखन; अपठित गद्यांश',
            'Mathematics' => 'Integers; fractions and decimals; algebra basics; ratio and proportion; geometry (lines, angles, triangles); mensuration; data handling',
            'Science' => 'Food and nutrition; materials and their properties; the world of living organisms; motion and force; natural phenomena; natural resources',
            'Social Science' => 'History (ancient, medieval and early modern India); geography (earth, climate, resources); civics (government and democracy)',
            'Sanskrit' => 'वर्णमाला व शब्द रूप; धातु रूप; संधि व समास; सरल संस्कृत गद्य-पद्य; अनुवाद अभ्यास',
        ];

        $secondary = [
            'English' => 'Prose and poetry; grammar (tenses, modals, reported speech); writing skills (letter, article, story); reading comprehension',
            'Hindi' => 'गद्य व पद्य संकलन; व्याकरण; निबंध व पत्र लेखन; अपठित बोध',
            'Mathematics' => 'Real numbers; polynomials; linear equations; triangles and circles; introduction to trigonometry; mensuration; statistics and probability',
            'Science' => 'Chemical reactions and equations; acids, bases and salts; life processes; light and electricity; natural resource management',
            'Social Science' => 'History (nationalism, world wars); geography (resources, agriculture); political science (democracy, political parties); economics (development, sectors of economy)',
        ];

        $englishCore = 'Prose and poetry; note-making and summarising; letter and essay writing; advanced grammar and vocabulary';
        $economics = 'Microeconomics (demand and supply); macroeconomics (national income); Indian economic development; money and banking';

        $streams = [
            'Science' => [
                'Physics' => 'Class 11: units and measurement, laws of motion, work-energy-power, gravitation, thermodynamics. Class 12: electrostatics, current electricity, magnetism, optics, modern physics',
                'Chemistry' => 'Class 11: structure of atom, periodic table, chemical bonding, states of matter, organic chemistry basics. Class 12: solutions, electrochemistry, chemical kinetics, p/d/f-block elements, biomolecules',
                'Mathematics' => 'Class 11: sets and functions, trigonometry, complex numbers, permutations and combinations, sequences and series. Class 12: relations and functions, calculus, vectors and 3D geometry, probability',
                'Biology' => 'Class 11: diversity of living organisms, cell structure, plant and human physiology. Class 12: reproduction, genetics and evolution, biotechnology, ecology',
                'English Core' => $englishCore,
                'Computer Science' => 'Programming fundamentals (Python); data structures; database concepts (SQL); computer networks; boolean logic and number systems',
            ],
            'Commerce' => [
                'Accountancy' => 'Accounting principles; journal, ledger and trial balance; partnership accounts; company accounts; financial statement analysis',
                'Business Studies' => 'Nature and forms of business; forms of organisation; business services; principles of management; marketing management; consumer protection',
                'Economics' => $economics,
                'Mathematics (Applied)' => 'Algebra; calculus basics; probability and statistics; financial mathematics; linear programming',
                'English Core' => $englishCore,
            ],
            'Arts' => [
                'History' => 'Ancient India; medieval India; modern India and the freedom struggle; world history',
                'Political Science' => 'Political theory; Indian constitution; comparative politics; international relations',
                'Geography' => 'Physical geography; human geography; Indian geography; map work and practical geography',
                'Economics' => $economics,
                'English Core' => $englishCore,
                'Sociology' => 'Introduction to sociology; Indian society; social change and social order; social institutions',
            ],
        ];

        foreach ($boards as $board) {
            foreach (range(1, 12) as $class) {
                $meta = ['board' => $board, 'education_level' => 'school'];

                if ($class <= 2) {
                    $this->createCategoryWithSubjects("Class {$class} ({$board})", $meta, $primaryLower);
                } elseif ($class <= 5) {
                    $this->createCategoryWithSubjects("Class {$class} ({$board})", $meta, $primaryUpper);
                } elseif ($class <= 8) {
                    $this->createCategoryWithSubjects("Class {$class} ({$board})", $meta, $middle);
                } elseif ($class <= 10) {
                    $this->createCategoryWithSubjects("Class {$class} ({$board})", $meta, $secondary);
                } else {
                    foreach ($streams as $streamName => $streamSubjects) {
                        $this->createCategoryWithSubjects("Class {$class} {$streamName} ({$board})", $meta, $streamSubjects);
                    }
                }
            }
        }
    }

    protected function seedHigherEducation(): void
    {
        // RGPV: Rajiv Gandhi Proudyogiki Vishwavidyalaya (Madhya Pradesh).
        // AKTU: Dr. A.P.J. Abdul Kalam Technical University (Uttar Pradesh).
        $universities = ['RGPV', 'AKTU'];

        $firstYearCommon = [
            'Engineering Mathematics I & II' => 'Differential and integral calculus; matrices and determinants; differential equations; vector calculus; Laplace transforms',
            'Engineering Physics' => 'Oscillations and waves; optics; quantum mechanics basics; semiconductor physics; laser and fibre optics',
            'Engineering Chemistry' => 'Water treatment; fuels and combustion; polymers; corrosion and its prevention; electrochemistry',
            'Basic Electrical & Electronics Engineering' => 'DC and AC circuits; electrical machines basics; semiconductor devices; digital electronics fundamentals',
            'Engineering Graphics & Computer Programming' => 'Orthographic projections; isometric views; basics of C programming; problem solving using algorithms and flowcharts',
        ];

        $dsa = 'Arrays, linked lists, stacks and queues, trees and graphs, sorting and searching, complexity analysis';
        $dbms = 'ER modelling, relational algebra, SQL, normalization, transactions and concurrency control';
        $os = 'Process management, CPU scheduling, memory management, deadlocks, file systems';
        $networks = 'OSI and TCP/IP models, routing, network security basics, application layer protocols';
        $mechanics = 'Statics and dynamics, friction, centroid and moment of inertia, kinematics of rigid bodies';
        $som = 'Stress and strain, bending moment and shear force, torsion, deflection of beams';

        $beBranches = [
            'Computer Science Engineering' => [
                'Programming in C & C++' => 'Data types, control structures, functions, arrays and strings, pointers, object-oriented basics',
                'Data Structures & Algorithms' => $dsa,
                'Database Management Systems' => $dbms,
                'Operating Systems' => $os,
                'Computer Networks' => $networks,
                'Object-Oriented Programming with Java' => 'Classes and objects, inheritance, polymorphism, exception handling, collections framework',
                'Software Engineering' => 'SDLC models, requirements analysis, design principles, testing strategies, project management',
                'Theory of Computation' => 'Finite automata, regular languages, context-free grammars, Turing machines, computability',
            ],
            'Information Technology' => [
                'Web Technologies' => 'HTML, CSS, JavaScript, server-side scripting, web frameworks basics',
                'Data Structures & Algorithms' => $dsa,
                'Database Management Systems' => $dbms,
                'Computer Networks' => $networks,
                'Operating Systems' => $os,
                'Information Security' => 'Cryptography basics, network security, access control, security protocols',
                'Cloud Computing' => 'Virtualization, cloud service models (IaaS/PaaS/SaaS), cloud deployment models, cloud security',
            ],
            'Electronics & Communication Engineering' => [
                'Electronic Devices & Circuits' => 'Diodes, transistors, amplifiers, oscillators, power supplies',
                'Digital Electronics' => 'Boolean algebra, logic gates, combinational and sequential circuits, counters and registers',
                'Analog Communication' => 'Amplitude and frequency modulation, transmitters and receivers, noise in communication systems',
                'Signals & Systems' => 'Signal classification, Fourier and Laplace transforms, convolution, sampling theorem',
                'Microprocessors & Microcontrollers' => '8085/8086 architecture, instruction sets, interfacing, microcontroller programming',
                'Digital Signal Processing' => 'Discrete-time signals, Z-transform, digital filters, FFT',
                'Antenna & Wave Propagation' => 'Antenna fundamentals, antenna arrays, wave propagation modes, microwave engineering basics',
            ],
            'Electrical Engineering' => [
                'Electrical Circuit Theory' => 'Network theorems, AC/DC circuit analysis, resonance, two-port networks',
                'Electrical Machines' => 'Transformers, DC machines, induction motors, synchronous machines',
                'Power Systems' => 'Generation, transmission and distribution, load flow analysis, fault analysis',
                'Control Systems' => 'Transfer functions, block diagrams, stability analysis, root locus, frequency response',
                'Power Electronics' => 'Power semiconductor devices, rectifiers, inverters, choppers',
                'Electrical Measurements' => 'Measuring instruments, bridges, transducers, instrumentation basics',
                'Switchgear & Protection' => 'Circuit breakers, relays, protection schemes for generators, transformers and transmission lines',
            ],
            'Mechanical Engineering' => [
                'Engineering Mechanics' => $mechanics,
                'Thermodynamics' => 'Laws of thermodynamics, entropy, thermodynamic cycles, properties of pure substances',
                'Fluid Mechanics' => 'Fluid statics and dynamics, Bernoulli equation, flow through pipes, turbines and pumps',
                'Strength of Materials' => $som,
                'Manufacturing Processes' => 'Casting, welding, machining processes, forming operations',
                'Machine Design' => 'Design of shafts, keys, couplings, bearings, gears',
                'Heat & Mass Transfer' => 'Conduction, convection, radiation, heat exchangers, mass transfer basics',
            ],
            'Civil Engineering' => [
                'Engineering Mechanics' => $mechanics,
                'Surveying' => 'Chain and compass surveying, levelling, theodolite surveying, total station basics',
                'Strength of Materials' => $som,
                'Structural Analysis' => 'Determinate and indeterminate structures, influence lines, slope-deflection method',
                'Concrete Technology' => 'Properties of concrete, mix design, testing of concrete, durability',
                'Geotechnical Engineering' => 'Soil classification, soil mechanics, bearing capacity, foundations',
                'Transportation Engineering' => 'Highway planning, pavement design, traffic engineering basics',
                'Environmental Engineering' => 'Water supply and treatment, wastewater treatment, solid waste management, air pollution control',
            ],
        ];

        $mtechSpecializations = [
            'Computer Science & Engineering' => [
                'Advanced Data Structures' => 'AVL and red-black trees, B-trees, hashing techniques, advanced graph algorithms',
                'Advanced Algorithms' => 'Divide and conquer, dynamic programming, greedy methods, NP-completeness, approximation algorithms',
                'Machine Learning' => 'Supervised and unsupervised learning, neural networks, model evaluation, deep learning basics',
                'Advanced Database Systems' => 'Distributed databases, NoSQL systems, query optimization, big data storage',
                'Distributed Systems' => 'Distributed computing models, consensus algorithms, fault tolerance, cloud architectures',
                'Research Methodology' => 'Research design, literature review, technical writing, data analysis and interpretation',
            ],
            'VLSI Design' => [
                'Advanced Digital Design' => 'FPGA architecture, hardware description languages (Verilog/VHDL), digital system design',
                'Analog IC Design' => 'MOSFET modelling, amplifier design, op-amp design, analog layout basics',
                'VLSI Testing' => 'Fault models, test pattern generation, design for testability, built-in self-test',
                'Semiconductor Device Modeling' => 'MOS device physics, short-channel effects, device scaling, SPICE modelling',
                'FPGA-based System Design' => 'FPGA programming, system-on-chip design, hardware-software co-design',
            ],
            'Structural Engineering' => [
                'Advanced Structural Analysis' => 'Matrix methods, energy methods, stiffness and flexibility approaches',
                'Earthquake Engineering' => 'Seismic design principles, response spectrum method, ductility and detailing',
                'Finite Element Method' => 'FEM formulation, element types, meshing, structural applications',
                'Advanced Concrete Design' => 'Limit state design, prestressed concrete, design of slabs and shells',
                'Structural Dynamics' => 'Single and multi-degree-of-freedom systems, free and forced vibration, damping',
            ],
            'Thermal Engineering' => [
                'Advanced Thermodynamics' => 'Availability and irreversibility, thermodynamic relations, gas mixtures',
                'Heat Transfer' => 'Advanced conduction, convection correlations, radiation exchange, boiling and condensation',
                'Refrigeration & Air Conditioning' => 'Vapour compression and absorption systems, psychrometry, HVAC design basics',
                'Computational Fluid Dynamics' => 'Governing equations, discretization methods, turbulence modelling, CFD software basics',
                'IC Engines' => 'Combustion in SI and CI engines, engine performance, emission control',
            ],
            'Power System' => [
                'Power System Analysis' => 'Load flow studies, symmetrical and unsymmetrical fault analysis, stability analysis',
                'Power System Protection' => 'Relay coordination, protection of generators, transformers and feeders, digital protection',
                'HVDC Transmission' => 'HVDC converter operation, control strategies, comparison with AC transmission',
                'Renewable Energy Systems' => 'Solar and wind energy systems, grid integration, energy storage basics',
                'Power Quality' => 'Harmonics, voltage sag/swell, power factor improvement, power quality standards',
            ],
        ];

        foreach ($universities as $university) {
            $this->createCategoryWithSubjects(
                "B.E. First Year (Common) ({$university})",
                ['university' => $university, 'education_level' => 'undergraduate'],
                $firstYearCommon
            );

            foreach ($beBranches as $branch => $subjects) {
                $this->createCategoryWithSubjects(
                    "B.E. {$branch} ({$university})",
                    ['university' => $university, 'education_level' => 'undergraduate'],
                    $subjects
                );
            }

            foreach ($mtechSpecializations as $specialization => $subjects) {
                $this->createCategoryWithSubjects(
                    "M.Tech {$specialization} ({$university})",
                    ['university' => $university, 'education_level' => 'postgraduate'],
                    $subjects
                );
            }
        }
    }

    /**
     * @param  array<string, string>  $subjects  subject name => syllabus outline
     */
    protected function createCategoryWithSubjects(string $categoryName, array $categoryAttrs, array $subjects): void
    {
        $category = SubjectCategory::firstOrCreate(
            ['slug' => Str::slug($categoryName)],
            array_merge(['name' => $categoryName], $categoryAttrs)
        );

        foreach ($subjects as $name => $syllabus) {
            Subject::firstOrCreate(
                ['slug' => Str::slug("{$categoryName}-{$name}")],
                [
                    'subject_category_id' => $category->id,
                    'name' => $name,
                    'syllabus' => $syllabus,
                    'is_active' => true,
                ]
            );
        }
    }
}

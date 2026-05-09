<footer>
    <div class="container">
        <div id="audit">
            <h4>Audit Calitate</h4>
            <p>Efectuăm verificări riguroase asupra integrității datelor pentru a asigura acuratețea rapoartelor,
                minimizând erorile umane.</p>
        </div>
        <div id="garantie">
            <h4>Garanție Servicii</h4>
            <p>Sistemul nostru beneficiază de suport tehnic extins, garantând remedierea oricărei neconformități în timp
                record.</p>
        </div>
        <div id="standarde">
            <h4>Standarde 2024</h4>
            <p>Ne aliniem anual la cele mai noi reglementări internaționale de securitate cibernetică și management al
                proceselor.</p>
        </div>
    </div>
    <div id="license">
        <h5>© <?= date('Y') ?> Enterprise Resource Planning.</h5>
        <h6>Inteligență Operațională Aplicată. Toate drepturile rezervate.</h6>
    </div>
</footer>

<?php if (!empty($scripts)): ?>
    <?php foreach ($scripts as $script): ?>
        <script src="/html5/<?= htmlspecialchars($script) ?>"></script>
    <?php endforeach; ?>
<?php endif; ?>

</body>
</html>
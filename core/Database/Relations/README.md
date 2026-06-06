Complet, extensible, et proprement typé. Ajouter une nouvelle relation demain c'est créer une classe qui étend AbstractRelation, implémenter hydrate() et applyJoin(), et l'ajouter dans resolveRelations() du repository concerné — rien d'autre ne change.

Core\Database\Relations\
    RelationInterface       — contrat commun
    AbstractRelation        — logique partagée (extract, groupRows, flatRows, getColumns)
    ManyToManyRelation      — groupRows + joinManyToMany + getColumns surchargé
    OneToManyRelation       — groupRows + joinLeft
    ManyToOneRelation       — flatRows + joinLeft
    OneToOneRelation        — flatRows + joinLeft
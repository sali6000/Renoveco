Core\Database\Relations\
    RelationInterface       — contrat commun
    AbstractRelation        — logique partagée (extract, groupRows, flatRows, getColumns)
    ManyToManyRelation      — groupRows + joinManyToMany + getColumns surchargé
    OneToManyRelation       — groupRows + joinLeft
    ManyToOneRelation       — flatRows + joinLeft
    OneToOneRelation        — flatRows + joinLeft
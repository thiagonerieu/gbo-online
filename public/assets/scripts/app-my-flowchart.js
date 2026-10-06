// 1. create an array with nodes
let nodes = new vis.DataSet([
    { id: 1, label: "1" },
    { id: 2, label: "2" },
    { id: 3, label: "3" },
    { id: 4, label: "4" },
    { id: 5, label: "5" },
    { id: 6, label: "6" },
    { id: 7, label: "7" },
    { id: 8, label: "*8*" },
    { id: 9, label: "*9*" },
    { id: 10, label: "10" },
    { id: 11, label: "*11*" },
    { id: 12, label: "*12*" },
]);

// 2. create an array with edges
let edges = new vis.DataSet([
    { from: 1, to: 3 },
    { from: 1, to: 2 },
    { from: 2, to: 1 },
    { from: 2, to: 4 },
    { from: 2, to: 5 },
    { from: 3, to: 7 },
    { from: 3, to: 9 },
    { from: 4, to: 8 },
    { from: 4, to: 10 },
    { from: 5, to: 6 },
    { from: 5, to: 11 },
    { from: 8, to: 9 },
    { from: 8, to: 12 },
]);

// 3. create a network
let container = document.getElementById("myflowchart");
let data = {
    nodes: nodes,
    edges: edges,
};
let options = {
    // 1. Configurações globais para todos os NÓS (Nodes)
    nodes: {
        shape: "circle", // Formato do nó (ex: dot, box, circle)
        size: 20,     // Tamanho dos nós
        color: {
            background: "#334155",    // Cor de fundo padrão (Azul)
            border: "#E2E8F0",        // Cor da borda padrão
            highlight: {                // Cor quando o usuário clica
                background: "#ffeb3b",
                border: "#f57f17"
            }
        },
        font: {
            color: "#E2E8F0", // Cor do texto do nó
            size: 18,            // Tamanho da fonte            
            align: "center",
            multi: "md",     // Permite usar *negrito* no label de qualquer nó
        }
    },
    
    // 2. Configurações globais para todas as LINHAS (Edges)
    edges: {
        arrows: {
            to: { 
                enabled: true,        // Ativa a seta apontando para o nó de destino ("to")
                scaleFactor: 0.7,     // Tamanho da ponta da seta
                type: "arrow"         // Estilo da seta ("arrow", "bar", "circle")
            }
        },
        color: {
            color: "#999999",     // Cor padrão da linha (Cinza)
            highlight: "#ff0000", // Cor da linha quando selecionada (Vermelho)
            inherit: false        // Importante: false para a linha usar a própria cor e não herdar a cor do nó
        },
        smooth: {
            enabled: true,        // Deixa as linhas levemente curvadas
            type: "continuous"    // Evita que setas de ida e volta se sobreponham
        }
    },

    // 3. Outras configurações globais (Opcional)
    physics: {
        enabled: true,            // Ativa ou desativa a física do grafo
        stabilization: true       // Evita o grafo ficar "pulando" ao carregar
    },
    interaction: {
        hover: true               // Ativa o efeito de passar o mouse por cima
    }
};

// 4. Inicializa o grafo aplicando as opções globais
let network = new vis.Network(container, data, options);

// Detecta clique simples em qualquer lugar do container
network.on("click", function (params) {
    // params.nodes contém um array com o ID do nó clicado (se houver)
    if (params.nodes.length > 0) {
        const nodeId = params.nodes[0];
        const dadosDoNo = nodes.get(nodeId); // Busca as propriedades do nó
        console.log("Usuário clicou no nó:", dadosDoNo.label);
    } else if (params.edges.length > 0) {
        console.log("Usuário clicou em uma linha (aresta):", params.edges[0]);
    } else {
        console.log("Usuário clicou no fundo vazio.");
    }
});

// Detecta clique duplo
network.on("doubleClick", function (params) {
    if (params.nodes.length > 0) {
        alert("Clique duplo no nó: " + params.nodes[0]);
    }
});

// 🟢 ADICIONAR: Cria um novo nó e conecta ao nó 1
function criarNovoNo() {
    nodes.add({ id: 99, label: "Nó Novo", color: "#ff5722" });
    edges.add({ from: 1, to: 99 });
}

// 🟡 ATUALIZAR: Muda a cor ou texto de um nó existente (obrigatório passar o id)
function destacarNo(id) {
    nodes.update({ id: id, color: { background: "#00ff00" }, font: { size: 20 } });
}

// 🔴 REMOVER: Deleta o nó do grafo
function deletarNo(id) {
    nodes.remove({ id: id }); // Remove o nó e limpa as linhas ligadas a ele automaticamente
}

// Faz a câmera dar zoom e focar em um nó específico com animação
function focarNoNo(id) {
    network.focus(id, {
        scale: 1.5,             // Nível do zoom (1 é o padrão)
        animation: {
            duration: 1000,     // Tempo em milissegundos
            easingFunction: "easeInOutQuad"
        }
    });
}

// Ajusta o zoom para que TODO o grafo caiba na tela novamente
function resetarVisao() {
    network.fit({ animation: true });
}

// Pega os IDs de todos os nós selecionados atualmente pelo usuário
const nosSelecionados = network.getSelectedNodes(); 

// Pega a posição X e Y de um nó na tela (útil para abrir menus customizados)
const posicao = network.getPositions([1]); 
console.log("Posição do nó 1:", posicao[1].x, posicao[1].y);

// Escuta quando a janela muda de tamanho
window.addEventListener("resize", () => {
    // 1. Força o vis.js a recalcular o tamanho do canvas interno
    network.redraw(); 
    
    // 2. Opcional: Ajusta o zoom para que todos os nós continuem visíveis na tela nova
    network.fit({
        animation: { duration: 300 } // Animação suave de 300ms
    });
});



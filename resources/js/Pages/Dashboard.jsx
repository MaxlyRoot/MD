import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import { Head } from "@inertiajs/react";
import { useState, useEffect } from "react";
import axios from "axios";

export default function Dashboard({ auth }) {
    const [data, setData] = useState([]);
    const [categories, setCategories] = useState([]);
    const [search, setSearch] = useState("");
    const [editData, setEditData] = useState(null);
    const [showEndpoints, setShowEndpoints] = useState(false);

    useEffect(() => {
        fetchExcelData();
        fetchCategories();
    }, []);

    const fetchExcelData = async () => {
        try {
            const response = await axios.get("/api/excel-data");
            setData(response.data);
        } catch (error) {
            console.error("Error fetching data:", error);
        }
    };

    const fetchCategories = async () => {
        try {
            const response = await axios.get("/api/categories");
            setCategories(response.data);
        } catch (error) {
            console.error("Error fetching categories:", error);
        }
    };

    const openEditModal = (item) => {
        setEditData({ ...item, category: item.category || "" });
    };

    const closeEditModal = () => {
        setEditData(null);
    };

    const handleEditSave = async () => {
        try {
            await axios.put(`/api/excel-data/${editData.id}`, {
                value: editData.value,
                category: editData.category,
            });
            fetchExcelData();
            closeEditModal();
        } catch (error) {
            console.error("Error updating data:", error);
            alert("Hubo un error al guardar el dato.");
        }
    };

    const handleDelete = async (id) => {
        if (!confirm("¿Estás seguro de que deseas eliminar este dato?")) {
            return;
        }
        try {
            await axios.delete(`/api/excel-data/${id}`);
            alert("Dato eliminado con éxito.");
            fetchExcelData();
        } catch (error) {
            console.error("Error deleting data:", error);
            alert("Hubo un error al eliminar el dato.");
        }
    };

    const handleHighlightCell = async (coordinateId) => {
        try {
            const response = await axios.get(`/api/highlight-cell/${coordinateId}`, {
                responseType: "blob",
            });

            const url = window.URL.createObjectURL(new Blob([response.data]));
            const link = document.createElement("a");
            link.href = url;
            link.setAttribute("download", "highlighted.xlsx");
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        } catch (error) {
            console.error("Error al generar el archivo:", error);
            alert("Hubo un error al procesar el archivo.");
        }
    };

    const handleSearch = (e) => {
        setSearch(e.target.value.toLowerCase());
    };

    const handleFileChange = async (event) => {
        const file = event.target.files[0];
        if (!file) {
            alert("Por favor selecciona un archivo.");
            return;
        }

        const formData = new FormData();
        formData.append("file", file);

        try {
            const response = await axios.post("/api/import-reports", formData, {
                headers: { "Content-Type": "multipart/form-data" },
            });
            alert(response.data.message || "Archivo cargado con éxito.");
            fetchExcelData();
        } catch (error) {
            console.error("Error al subir el archivo:", error);
            alert("Hubo un error al procesar el archivo.");
        }
    };

    const filteredData = data.filter((item) =>
        item.value.toLowerCase().includes(search) ||
        item.coordinate.row.toString().includes(search) ||
        item.coordinate.column.toString().includes(search) ||
        (item.category || "").toLowerCase().includes(search)
    );

    return (
        <AuthenticatedLayout
            user={auth.user}
            header={<h2 className="font-semibold text-xl text-gray-800 leading-tight">Carga y Visualización de Datos</h2>}
        >
            <Head title="Dashboard" />

            <div className="py-12">
                <div className="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <div className="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div className="p-6 text-gray-900">
                            <h3 className="font-medium text-lg text-gray-700 mb-4">Subir Archivo</h3>
                            <input
                                type="file"
                                className="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 mb-4"
                                onChange={handleFileChange}
                            />

                            <h3 className="font-medium text-lg text-gray-700 mb-4">Buscar Datos</h3>
                            <input
                                type="text"
                                placeholder="Buscar por valor, fila, columna o categoría"
                                className="w-full border border-gray-300 rounded-lg py-2 px-4 text-gray-900 mb-4"
                                value={search}
                                onChange={handleSearch}
                            />

                            <button
                                className="bg-blue-500 hover:bg-blue-600 text-white py-2 px-4 rounded mb-4"
                                onClick={() => setShowEndpoints((prev) => !prev)}
                            >
                                {showEndpoints ? "Ocultar Endpoints" : "Ver Endpoints"}
                            </button>

                            {showEndpoints && (
                                <div className="fixed inset-0 bg-gray-800 bg-opacity-50 flex justify-center items-center z-50">
                                    <div className="bg-white p-6 rounded shadow-md w-96">
                                        <h3 className="font-semibold text-lg text-gray-800 mb-4">Endpoints Disponibles</h3>
                                        <ul className="list-disc list-inside text-gray-700">
                                            <li><strong>Subir Archivo:</strong> <code>POST /api/import-reports</code></li>
                                            <li><strong>Obtener Datos:</strong> <code>GET /api/excel-data</code></li>
                                            <li><strong>Actualizar Dato:</strong> <code>PUT /api/excel-data/{'{id}'}</code></li>
                                            <li><strong>Eliminar Dato:</strong> <code>DELETE /api/excel-data/{'{id}'}</code></li>
                                            <li><strong>Obtener Categorías:</strong> <code>GET /api/categories</code></li>
                                            <li><strong>Resaltar Dato en Excel:</strong> <code>GET /api/highlight-cell/{'{coordinateId}'}</code></li>
                                        </ul>
                                        <button
                                            className="mt-4 bg-gray-500 hover:bg-gray-600 text-white py-1 px-3 rounded"
                                            onClick={() => setShowEndpoints(false)}
                                        >
                                            Cerrar
                                        </button>
                                    </div>
                                </div>
                            )}

                            <table className="table-auto w-full border-collapse border border-gray-300">
                                <thead>
                                    <tr>
                                        <th className="border px-4 py-2">Fila</th>
                                        <th className="border px-4 py-2">Columna</th>
                                        <th className="border px-4 py-2">Valor</th>
                                        <th className="border px-4 py-2">Categoría</th>
                                        <th className="border px-4 py-2">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {filteredData.map((item) => (
                                        <tr key={item.id}>
                                            <td className="border px-4 py-2 text-center">{item.coordinate.row}</td>
                                            <td className="border px-4 py-2 text-center">{item.coordinate.column}</td>
                                            <td className="border px-4 py-2 text-center">{item.value}</td>
                                            <td className="border px-4 py-2 text-center">{item.category || "Sin categoría"}</td>
                                            <td className="border px-4 py-2 text-center">
                                                <div className="flex space-x-4 justify-center">
                                                    <button
                                                        className="bg-blue-500 hover:bg-blue-600 text-white py-1 px-3 rounded"
                                                        onClick={() => handleHighlightCell(item.coordinate.id)}
                                                    >
                                                        Ver en Excel
                                                    </button>
                                                    <button
                                                        className="bg-yellow-500 hover:bg-yellow-600 text-white py-1 px-3 rounded"
                                                        onClick={() => openEditModal(item)}
                                                    >
                                                        Editar
                                                    </button>
                                                    <button
                                                        className="bg-red-500 hover:bg-red-600 text-white py-1 px-3 rounded"
                                                        onClick={() => handleDelete(item.id)}
                                                    >
                                                        Eliminar
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    ))}
                                </tbody>
                            </table>

                            {editData && (
                                <div className="fixed inset-0 bg-gray-800 bg-opacity-50 flex justify-center items-center">
                                    <div className="bg-white p-6 rounded shadow-md w-96">
                                        <h3 className="font-semibold text-lg text-gray-800 mb-4">Editar Dato</h3>
                                        <div className="mb-4">
                                            <label className="block text-sm font-medium text-gray-700">Valor</label>
                                            <input
                                                type="text"
                                                className="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 text-gray-900"
                                                value={editData.value}
                                                onChange={(e) => setEditData({ ...editData, value: e.target.value })}
                                            />
                                        </div>
                                        <div className="mb-4">
                                            <label className="block text-sm font-medium text-gray-700">Categoría</label>
                                            <select
                                                className="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 text-gray-900"
                                                value={editData.category}
                                                onChange={(e) => setEditData({ ...editData, category: e.target.value })}
                                            >
                                                <option value="">Seleccione una categoría</option>
                                                {categories.map((category) => (
                                                    <option key={category.id} value={category.name}>
                                                        {category.name}
                                                    </option>
                                                ))}
                                            </select>
                                        </div>
                                        <div className="flex justify-end space-x-4">
                                            <button
                                                className="bg-gray-500 hover:bg-gray-600 text-white py-1 px-3 rounded"
                                                onClick={closeEditModal}
                                            >
                                                Cancelar
                                            </button>
                                            <button
                                                className="bg-blue-500 hover:bg-blue-600 text-white py-1 px-3 rounded"
                                                onClick={handleEditSave}
                                            >
                                                Guardar
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            )}
                        </div>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
